<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * DashboardController
 * 
 * Handles dashboard display for both admin and member roles
 * 
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role
     * 
     * @return View
     */
    public function index(): View
    {
        if (auth()->user()->hasRole('admin')) {
            return $this->admin();
        }
        
        return $this->member();
    }

    /**
     * Display admin dashboard with system-wide statistics
     * 
     * @return View
     */
    private function admin(): View
    {
        $totalMembers = User::count();
        $totalWalletBalance = Wallet::sum('balance');
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->count();
        $totalPinPoints = User::sum('pin_point');
        $recentTransactions = WalletTransaction::with(['wallet.user'])
            ->latest()
            ->take(10)
            ->get();
        $recentMembers = User::latest()->take(10)->get();

        return view('dashboard.admin', compact(
            'totalMembers',
            'totalWalletBalance',
            'pendingWithdrawals',
            'totalPinPoints',
            'recentTransactions',
            'recentMembers'
        ));
    }

    /**
     * Display member dashboard with personal statistics
     * 
     * @return View
     */
    private function member(): View
    {
        $user = auth()->user();
        
        $walletBalance = $user->wallet->balance ?? 0;
        $pinBalance = $user->pin_point;
        
        // Calculate commission earned
        $totalCommission = $user->wallet?->transactions()
            ->where('reference_type', 'commission')
            ->sum('amount');
        
        // Count total downlines
        $totalDownlines = $user->downlineMembers()->count();
        
        // Count active downlines
        $activeDownlines = $user->downlineMembers()
            ->whereHas('member', function ($query) {
                $query->where('status', 'active');
            })
            ->count();
        
        // Pending withdrawal requests
        $pendingWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        
        // Recent transactions
        $recentTransactions = $user->wallet?->transactions()
            ->latest()
            ->take(10)
            ->get() ?? [];

        return view('dashboard.member', compact(
            'walletBalance',
            'pinBalance',
            'totalCommission',
            'totalDownlines',
            'activeDownlines',
            'pendingWithdrawals',
            'recentTransactions'
        ));
    }
}

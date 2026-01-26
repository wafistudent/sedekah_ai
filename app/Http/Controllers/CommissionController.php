<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * CommissionController
 * 
 * Handles commission-related operations and reports
 * 
 * @package App\Http\Controllers
 */
class CommissionController extends Controller
{
    /**
     * Display commission transaction history
     * 
     * @return View
     */
    public function index(): View
    {
        $commissions = WalletTransaction::where('user_id', auth()->id())
            ->where('reference_type', 'commission')
            ->with(['wallet.user'])
            ->latest()
            ->paginate(20);

        $totalCommission = WalletTransaction::where('user_id', auth()->id())
            ->where('reference_type', 'commission')
            ->sum('amount');

        return view('commissions.index', compact('commissions', 'totalCommission'));
    }

    /**
     * Display commission summary by level
     * 
     * @return View
     */
    public function summary(): View
    {
        $wallet = auth()->user()->wallet;
        
        if (!$wallet) {
            $totalCommission = 0;
            $commissionsByLevel = collect([]);
        } else {
            // Total commission for the card display
            $totalCommission = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('reference_type', 'commission')
                ->sum('amount');

            // Get commission data for the level cards
            $commissionsByLevel = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('reference_type', 'commission')
                ->select('level', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
                ->whereNotNull('level')
                ->groupBy('level')
                ->orderBy('level')
                ->get();
        }

        return view('commissions.summary', compact('commissionsByLevel', 'totalCommission'));
    }
}

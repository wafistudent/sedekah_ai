<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * WithdrawalController
 * 
 * Handles withdrawal requests for both admin and members
 * 
 * @package App\Http\Controllers
 */
class WithdrawalController extends Controller
{
    /**
     * @var WalletService
     */
    protected WalletService $walletService;

    /**
     * Constructor
     * 
     * @param WalletService $walletService
     */
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display all pending withdrawal requests (Admin only)
     * 
     * @return View
     */
    public function index(): View
    {
        $withdrawals = WithdrawalRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Display member's own withdrawal requests
     * 
     * @return View
     */
    public function myRequests(): View
    {
        $withdrawals = WithdrawalRequest::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        $danaAccount = auth()->user();

        return view('withdrawals.my-requests', compact('withdrawals' , 'danaAccount'));
    }

    /**
     * Approve a withdrawal request (Admin only)
     * 
     * @param string $id
     * @return RedirectResponse
     */
    public function approve(string $id): RedirectResponse
    {
        try {
            $withdrawal = WithdrawalRequest::findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                return redirect()->back()
                    ->with('error', 'This withdrawal request has already been processed');
            }

            // Debit from user's wallet
            $this->walletService->debit(
                userId: $withdrawal->user_id,
                amount: $withdrawal->amount,
                referenceType: 'withdrawal',
                referenceId: $withdrawal->id,
                description: "Withdrawal approved - Rp " . number_format($withdrawal->amount, 0, ',', '.')
            );

            // Update withdrawal status
            $withdrawal->update([
                'status' => 'approved',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            // Fire WithdrawalApproved event
            event(new \App\Events\WithdrawalApproved(
                member: $withdrawal->user,
                withdrawal: $withdrawal,
                admin: auth()->user()
            ));

            return redirect()->back()
                ->with('success', 'Withdrawal request approved successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Reject a withdrawal request (Admin only)
     * 
     * @param string $id
     * @return RedirectResponse
     */
    public function reject(string $id): RedirectResponse
    {
        try {
            $withdrawal = WithdrawalRequest::findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                return redirect()->back()
                    ->with('error', 'This withdrawal request has already been processed');
            }

            // Update withdrawal status
            $withdrawal->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            // Fire WithdrawalRejected event
            event(new \App\Events\WithdrawalRejected(
                member: $withdrawal->user,
                withdrawal: $withdrawal,
                admin: auth()->user(),
                reason: 'Tidak ada alasan' // Default reason - can be extended later
            ));

            return redirect()->back()
                ->with('success', 'Withdrawal request rejected');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject withdrawal: ' . $e->getMessage());
        }
    }
}

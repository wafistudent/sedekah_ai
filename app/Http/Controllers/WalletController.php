<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use App\Models\AppSetting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * WalletController
 * 
 * Handles wallet operations including viewing balance, transactions, and withdrawal requests
 * 
 * @package App\Http\Controllers
 */
class WalletController extends Controller
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
     * Display wallet balance and transaction history
     * 
     * @return View
     */
    public function index(): View
    {
        $user = auth()->user();
        $balance = $this->walletService->getBalance($user->id);

        return view('wallet.index', compact('balance'));
    }

    /**
     * Show withdrawal request form
     * 
     * @return View
     */
    public function withdrawal(): View
    {
        $user = auth()->user();
        $balance = $this->walletService->getBalance($user->id);
        $minWithdrawal = AppSetting::get('min_withdrawal', 100000);

        return view('wallet.withdrawal', compact('balance', 'minWithdrawal'));
    }

    /**
     * Process withdrawal request
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeWithdrawal(Request $request): RedirectResponse
    {
        $minWithdrawal = AppSetting::get('min_withdrawal', 100000);

        $validator = Validator::make($request->all(), [
            'amount' => "required|numeric|min:{$minWithdrawal}",
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();
        $amount = $request->amount;

        // Check if user can withdraw this amount
        if (!$this->walletService->canWithdraw($user->id, $amount)) {
            return redirect()->back()
                ->with('error', 'Insufficient balance or amount below minimum withdrawal')
                ->withInput();
        }

        try {
            // Create withdrawal request
            $withdrawal = WithdrawalRequest::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'bank_account' => "DANA - {$user->dana_name} ({$user->dana_number})",
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            // Fire WithdrawalRequested event
            event(new \App\Events\WithdrawalRequested(
                member: $user,
                withdrawal: $withdrawal,
                amount: $amount,
                bankInfo: [
                    'bank_name' => 'DANA',
                    'account_number' => $user->dana_number,
                    'account_name' => $user->dana_name,
                ]
            ));

            return redirect()->route('withdrawals.my-requests')
                ->with('success', 'Withdrawal request submitted successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit withdrawal request: ' . $e->getMessage())
                ->withInput();
        }
    }
}

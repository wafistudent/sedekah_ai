<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * WalletService
 * 
 * Handles wallet operations including balance management,
 * credit/debit transactions, and withdrawal validation
 * 
 * @package App\Services
 */
class WalletService
{
    /**
     * Create a wallet for a new user
     * 
     * @param string $userId
     * @return Wallet
     * @throws Exception
     */
    public function createWallet(string $userId): Wallet
    {
        // Validate user exists
        $user = User::find($userId);
        if (!$user) {
            throw new Exception("User with ID {$userId} not found");
        }

        // Check if wallet already exists
        $existingWallet = Wallet::where('user_id', $userId)->first();
        if ($existingWallet) {
            throw new Exception("Wallet already exists for user {$userId}");
        }

        return Wallet::create([
            'user_id' => $userId,
            'balance' => 0,
        ]);
    }

    /**
     * Add balance to a user's wallet
     * 
     * @param string $userId
     * @param float $amount
     * @param string $referenceType
     * @param string|null $referenceId
     * @param string|null $fromMemberId
     * @param int|null $level
     * @param string|null $description
     * @return WalletTransaction
     * @throws Exception
     */
    public function credit(
        string $userId,
        float $amount,
        string $referenceType,
        ?string $referenceId = null,
        ?string $fromMemberId = null,
        ?int $level = null,
        ?string $description = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new Exception("Credit amount must be greater than 0");
        }

        return DB::transaction(function () use (
            $userId,
            $amount,
            $referenceType,
            $referenceId,
            $fromMemberId,
            $level,
            $description
        ) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();

            if (!$wallet) {
                $this->createWallet($userId);
                $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore + $amount;

            // Update wallet balance
            $wallet->balance = $balanceAfter;
            $wallet->save();

            // Create transaction record
            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'from_member_id' => $fromMemberId,
                'level' => $level,
                'description' => $description,
            ]);

            // Fire CommissionReceived event if this is a commission credit
            if ($referenceType === 'commission') {
                $member = User::find($userId);
                $fromMember = $fromMemberId ? User::find($fromMemberId) : null;
                
                if ($member) {
                    event(new \App\Events\CommissionReceived(
                        member: $member,
                        commission: $transaction,
                        amount: $amount,
                        type: $description ?? "Commission Level {$level}",
                        fromMember: $fromMember
                    ));
                }
            }

            return $transaction;
        });
    }

    /**
     * Deduct balance from a user's wallet
     * 
     * @param string $userId
     * @param float $amount
     * @param string $referenceType
     * @param string|null $referenceId
     * @param string|null $description
     * @return WalletTransaction
     * @throws Exception
     */
    public function debit(
        string $userId,
        float $amount,
        string $referenceType,
        ?string $referenceId = null,
        ?string $description = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new Exception("Debit amount must be greater than 0");
        }

        return DB::transaction(function () use (
            $userId,
            $amount,
            $referenceType,
            $referenceId,
            $description
        ) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();

            if (!$wallet) {
                throw new Exception("Wallet not found for user {$userId}");
            }

            $balanceBefore = $wallet->balance;

            if ($balanceBefore < $amount) {
                throw new Exception("Insufficient balance. Available: {$balanceBefore}, Required: {$amount}");
            }

            $balanceAfter = $balanceBefore - $amount;

            // Update wallet balance
            $wallet->balance = $balanceAfter;
            $wallet->save();

            // Create transaction record
            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
            ]);
        });
    }

    /**
     * Get current balance for a user
     * 
     * @param string $userId
     * @return float
     * @throws Exception
     */
    public function getBalance(string $userId): float
    {
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            throw new Exception("Wallet not found for user {$userId}");
        }

        return (float) $wallet->balance;
    }

    /**
     * Check if user can withdraw specified amount
     * 
     * @param string $userId
     * @param float $amount
     * @return bool
     */
    public function canWithdraw(string $userId, float $amount): bool
    {
        try {
            $balance = $this->getBalance($userId);
            $minWithdrawal = AppSetting::get('min_withdrawal', 50000);

            return $balance >= $amount && $amount >= $minWithdrawal;
        } catch (Exception $e) {
            return false;
        }
    }
}

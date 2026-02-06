<?php

namespace App\Listeners;

use App\Events\CommissionReceived;
use App\Events\MemberRegistered;
use App\Events\WithdrawalApproved;
use App\Events\WithdrawalRejected;
use App\Events\WithdrawalRequested;
use App\Services\WhatsappMessageService;
use Illuminate\Support\Facades\Log;

/**
 * SendWhatsappNotification Listener
 *
 * Central listener to handle all WhatsApp notification events
 *
 * @package App\Listeners
 */
class SendWhatsappNotification
{
    /**
     * WhatsappMessageService instance
     *
     * @var WhatsappMessageService
     */
    protected $whatsappMessageService;

    /**
     * Create the event listener
     *
     * @param WhatsappMessageService $whatsappMessageService
     */
    public function __construct(WhatsappMessageService $whatsappMessageService)
    {
        $this->whatsappMessageService = $whatsappMessageService;
    }

    /**
     * Handle the event
     *
     * @param object $event
     * @return void
     */
    public function handle($event): void
    {
        try {
            if ($event instanceof MemberRegistered) {
                $this->handleMemberRegistered($event);
            } elseif ($event instanceof CommissionReceived) {
                $this->handleCommissionReceived($event);
            } elseif ($event instanceof WithdrawalRequested) {
                $this->handleWithdrawalRequested($event);
            } elseif ($event instanceof WithdrawalApproved) {
                $this->handleWithdrawalApproved($event);
            } elseif ($event instanceof WithdrawalRejected) {
                $this->handleWithdrawalRejected($event);
            }
        } catch (\Exception $e) {
            // CRITICAL: Never throw exception - graceful degradation
            Log::error("[WhatsApp Listener] Failed to process event", [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle MemberRegistered event
     *
     * @param MemberRegistered $event
     * @return void
     */
    protected function handleMemberRegistered(MemberRegistered $event): void
    {
        $user = $event->user;
        $sponsor = $event->sponsor;
        $upline = $event->upline;

        // Skip if user has no phone number
        if (empty($user->phone)) {
            Log::warning("[WhatsApp Listener] Cannot send welcome message - no phone number", [
                'user_id' => $user->id,
            ]);
            return;
        }

        // Prepare data for template
        $data = [
            'name' => $user->name,
            'username' => $user->id, // User ID is the username
            'email' => $user->email,
            'phone' => $user->phone,
            'sponsor_name' => $sponsor?->name ?? '-',
            'upline_name' => $upline?->name ?? '-',
            'join_date' => $user->created_at->format('d-m-Y'),
            'login_url' => url('/login'),
        ];

        $metadata = [
            'user_id' => $user->id,
            'event_type' => 'member_registered',
            'event_timestamp' => now()->toDateTimeString(),
        ];

        // Send welcome message to new member
        $this->whatsappMessageService->sendByTemplate(
            'welcome_new_member',
            $user->phone,
            $data,
            $metadata
        );

        Log::info("[WhatsApp Listener] Welcome message queued", [
            'user_id' => $user->id,
            'phone' => $user->phone,
        ]);
    }

    /**
     * Handle CommissionReceived event
     *
     * @param CommissionReceived $event
     * @return void
     */
    protected function handleCommissionReceived(CommissionReceived $event): void
    {
        $member = $event->member;
        $amount = $event->amount;
        $type = $event->type;
        $fromMember = $event->fromMember;

        // Skip if member has no phone number
        if (empty($member->phone)) {
            Log::warning("[WhatsApp Listener] Cannot send commission notification - no phone number", [
                'user_id' => $member->id,
            ]);
            return;
        }

        // Get balance from wallet
        $wallet = \App\Models\Wallet::where('user_id', $member->id)->first();
        $balance = $wallet ? $wallet->balance : 0;

        $data = [
            'name' => $member->name,
            'amount' => 'Rp ' . number_format($amount, 0, ',', '.'),
            'commission_type' => $type,
            'from_member' => $fromMember?->name ?? '-',
            'date' => now()->format('d-m-Y H:i'),
            'balance' => 'Rp ' . number_format($balance, 0, ',', '.'),
        ];

        $metadata = [
            'user_id' => $member->id,
            'commission_id' => $event->commission->id ?? null,
            'event_type' => 'commission_received',
        ];

        $this->whatsappMessageService->sendByTemplate(
            'commission_received',
            $member->phone,
            $data,
            $metadata
        );

        Log::info("[WhatsApp Listener] Commission notification queued", [
            'user_id' => $member->id,
            'amount' => $amount,
        ]);
    }

    /**
     * Handle WithdrawalRequested event
     *
     * @param WithdrawalRequested $event
     * @return void
     */
    protected function handleWithdrawalRequested(WithdrawalRequested $event): void
    {
        $member = $event->member;
        $withdrawal = $event->withdrawal;
        $bankInfo = $event->bankInfo;

        // Skip if member has no phone number
        if (empty($member->phone)) {
            Log::warning("[WhatsApp Listener] Cannot send withdrawal request notification - no phone number", [
                'user_id' => $member->id,
            ]);
            return;
        }

        $data = [
            'name' => $member->name,
            'amount' => 'Rp ' . number_format($event->amount, 0, ',', '.'),
            'bank_name' => $bankInfo['bank_name'] ?? '-',
            'account_number' => $bankInfo['account_number'] ?? '-',
            'account_name' => $bankInfo['account_name'] ?? '-',
            'date' => now()->format('d-m-Y H:i'),
        ];

        $metadata = [
            'user_id' => $member->id,
            'withdrawal_id' => $withdrawal->id ?? null,
            'event_type' => 'withdrawal_requested',
        ];

        $this->whatsappMessageService->sendByTemplate(
            'withdrawal_requested',
            $member->phone,
            $data,
            $metadata
        );

        Log::info("[WhatsApp Listener] Withdrawal request notification queued", [
            'user_id' => $member->id,
            'amount' => $event->amount,
        ]);
    }

    /**
     * Handle WithdrawalApproved event
     *
     * @param WithdrawalApproved $event
     * @return void
     */
    protected function handleWithdrawalApproved(WithdrawalApproved $event): void
    {
        $member = $event->member;
        $withdrawal = $event->withdrawal;
        $admin = $event->admin;

        // Skip if member has no phone number
        if (empty($member->phone)) {
            Log::warning("[WhatsApp Listener] Cannot send withdrawal approved notification - no phone number", [
                'user_id' => $member->id,
            ]);
            return;
        }

        $data = [
            'name' => $member->name,
            'amount' => 'Rp ' . number_format($withdrawal->amount ?? 0, 0, ',', '.'),
            'bank_name' => 'DANA',
            'account_number' => $member->dana_number ?? '-',
            'account_name' => $member->dana_name ?? '-',
            'date' => now()->format('d-m-Y H:i'),
            'admin_name' => $admin->name,
        ];

        $metadata = [
            'user_id' => $member->id,
            'withdrawal_id' => $withdrawal->id ?? null,
            'admin_id' => $admin->id,
            'event_type' => 'withdrawal_approved',
        ];

        $this->whatsappMessageService->sendByTemplate(
            'withdrawal_approved',
            $member->phone,
            $data,
            $metadata
        );

        Log::info("[WhatsApp Listener] Withdrawal approved notification queued", [
            'user_id' => $member->id,
            'withdrawal_id' => $withdrawal->id ?? null,
        ]);
    }

    /**
     * Handle WithdrawalRejected event
     *
     * @param WithdrawalRejected $event
     * @return void
     */
    protected function handleWithdrawalRejected(WithdrawalRejected $event): void
    {
        $member = $event->member;
        $withdrawal = $event->withdrawal;
        $admin = $event->admin;
        $reason = $event->reason;

        // Skip if member has no phone number
        if (empty($member->phone)) {
            Log::warning("[WhatsApp Listener] Cannot send withdrawal rejected notification - no phone number", [
                'user_id' => $member->id,
            ]);
            return;
        }

        $data = [
            'name' => $member->name,
            'amount' => 'Rp ' . number_format($withdrawal->amount ?? 0, 0, ',', '.'),
            'date' => now()->format('d-m-Y H:i'),
            'reason' => $reason,
        ];

        $metadata = [
            'user_id' => $member->id,
            'withdrawal_id' => $withdrawal->id ?? null,
            'admin_id' => $admin->id,
            'event_type' => 'withdrawal_rejected',
        ];

        $this->whatsappMessageService->sendByTemplate(
            'withdrawal_rejected',
            $member->phone,
            $data,
            $metadata
        );

        Log::info("[WhatsApp Listener] Withdrawal rejected notification queued", [
            'user_id' => $member->id,
            'withdrawal_id' => $withdrawal->id ?? null,
            'reason' => $reason,
        ]);
    }
}

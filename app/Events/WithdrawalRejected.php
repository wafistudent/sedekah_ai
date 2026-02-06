<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * WithdrawalRejected Event
 *
 * Fired when an admin rejects a withdrawal request
 *
 * @package App\Events
 */
class WithdrawalRejected
{
    use Dispatchable, SerializesModels;

    /**
     * The member whose withdrawal was rejected
     *
     * @var User
     */
    public $member;

    /**
     * The withdrawal model
     *
     * @var mixed
     */
    public $withdrawal;

    /**
     * The admin who rejected the withdrawal
     *
     * @var User
     */
    public $admin;

    /**
     * The reason for rejection
     *
     * @var string
     */
    public $reason;

    /**
     * Create a new event instance
     *
     * @param User $member
     * @param mixed $withdrawal Withdrawal model
     * @param User $admin
     * @param string $reason
     */
    public function __construct(
        User $member,
        $withdrawal,
        User $admin,
        string $reason
    ) {
        $this->member = $member;
        $this->withdrawal = $withdrawal;
        $this->admin = $admin;
        $this->reason = $reason;
    }
}

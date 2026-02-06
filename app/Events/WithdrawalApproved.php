<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * WithdrawalApproved Event
 *
 * Fired when an admin approves a withdrawal request
 *
 * @package App\Events
 */
class WithdrawalApproved
{
    use Dispatchable, SerializesModels;

    /**
     * The member whose withdrawal was approved
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
     * The admin who approved the withdrawal
     *
     * @var User
     */
    public $admin;

    /**
     * Create a new event instance
     *
     * @param User $member
     * @param mixed $withdrawal Withdrawal model
     * @param User $admin
     */
    public function __construct(
        User $member,
        $withdrawal,
        User $admin
    ) {
        $this->member = $member;
        $this->withdrawal = $withdrawal;
        $this->admin = $admin;
    }
}

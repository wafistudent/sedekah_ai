<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * WithdrawalRequested Event
 *
 * Fired when a member requests a withdrawal
 *
 * @package App\Events
 */
class WithdrawalRequested
{
    use Dispatchable, SerializesModels;

    /**
     * The member requesting withdrawal
     *
     * @var User
     */
    public $member;

    /**
     * The withdrawal model (flexible type)
     *
     * @var mixed
     */
    public $withdrawal;

    /**
     * The withdrawal amount
     *
     * @var float
     */
    public $amount;

    /**
     * Bank account information
     *
     * @var array
     */
    public $bankInfo;

    /**
     * Create a new event instance
     *
     * @param User $member
     * @param mixed $withdrawal Withdrawal model (flexible type)
     * @param float $amount
     * @param array $bankInfo
     */
    public function __construct(
        User $member,
        $withdrawal,
        float $amount,
        array $bankInfo
    ) {
        $this->member = $member;
        $this->withdrawal = $withdrawal;
        $this->amount = $amount;
        $this->bankInfo = $bankInfo;
    }
}

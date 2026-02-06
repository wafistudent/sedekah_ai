<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * CommissionReceived Event
 *
 * Fired when a member receives a commission
 *
 * @package App\Events
 */
class CommissionReceived
{
    use Dispatchable, SerializesModels;

    /**
     * The member receiving the commission
     *
     * @var User
     */
    public $member;

    /**
     * The commission model (flexible type)
     *
     * @var mixed
     */
    public $commission;

    /**
     * The commission amount
     *
     * @var float
     */
    public $amount;

    /**
     * The type of commission
     *
     * @var string
     */
    public $type;

    /**
     * The member who triggered this commission
     *
     * @var User|null
     */
    public $fromMember;

    /**
     * Create a new event instance
     *
     * @param User $member
     * @param mixed $commission Commission model (flexible type)
     * @param float $amount
     * @param string $type
     * @param User|null $fromMember
     */
    public function __construct(
        User $member,
        $commission,
        float $amount,
        string $type,
        ?User $fromMember = null
    ) {
        $this->member = $member;
        $this->commission = $commission;
        $this->amount = $amount;
        $this->type = $type;
        $this->fromMember = $fromMember;
    }
}

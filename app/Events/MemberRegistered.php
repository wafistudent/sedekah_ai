<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * MemberRegistered Event
 *
 * Fired when a new member is registered in the system
 *
 * @package App\Events
 */
class MemberRegistered
{
    use Dispatchable, SerializesModels;

    /**
     * The newly registered user
     *
     * @var User
     */
    public $user;

    /**
     * The sponsor who registered this user
     *
     * @var User|null
     */
    public $sponsor;

    /**
     * The upline of this user
     *
     * @var User|null
     */
    public $upline;

    /**
     * Create a new event instance
     *
     * @param User $user
     * @param User|null $sponsor
     * @param User|null $upline
     */
    public function __construct(
        User $user,
        ?User $sponsor = null,
        ?User $upline = null
    ) {
        $this->user = $user;
        $this->sponsor = $sponsor;
        $this->upline = $upline;
    }
}

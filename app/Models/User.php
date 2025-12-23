<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 * 
 * Represents a user in the MLM system with PIN-based registration
 * 
 * @package App\Models
 * 
 * @property string $id Username (primary key)
 * @property string $email
 * @property string $password
 * @property string $name
 * @property string|null $phone
 * @property string $dana_name
 * @property string $dana_number
 * @property int $pin_point
 * @property string $status
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Indicates if the model's ID is auto-incrementing
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'email',
        'password',
        'name',
        'phone',
        'dana_name',
        'dana_number',
        'pin_point',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pin_point' => 'integer',
            'status' => 'string',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the network record for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function network()
    {
        return $this->hasOne(Network::class, 'member_id', 'id');
    }

    /**
     * Get the wallet for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    /**
     * Get all PIN transactions for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pinTransactions()
    {
        return $this->hasMany(PinTransaction::class, 'member_id', 'id');
    }

    /**
     * Get all withdrawal requests for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'user_id', 'id');
    }

    /**
     * Get all members sponsored by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sponsoredMembers()
    {
        return $this->hasMany(Network::class, 'sponsor_id', 'id');
    }

    /**
     * Get all downline members under this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downlineMembers()
    {
        return $this->hasMany(Network::class, 'upline_id', 'id');
    }
}

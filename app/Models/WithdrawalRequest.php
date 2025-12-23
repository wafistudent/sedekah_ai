<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * WithdrawalRequest Model
 * 
 * Represents withdrawal requests from users
 * 
 * @package App\Models
 * 
 * @property string $id UUID
 * @property string $user_id
 * @property float $amount
 * @property string $status
 * @property string|null $bank_account
 * @property string|null $notes
 * @property string|null $processed_by
 * @property \DateTime $requested_at
 * @property \DateTime|null $processed_at
 */
class WithdrawalRequest extends Model
{
    use HasFactory, UsesUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'bank_account',
        'notes',
        'processed_by',
        'requested_at',
        'processed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that made the withdrawal request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the admin user who processed the withdrawal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }
}

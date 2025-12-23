<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * WalletTransaction Model
 * 
 * Represents credit/debit transactions in user wallets
 * 
 * @package App\Models
 * 
 * @property string $id UUID
 * @property string $wallet_id
 * @property string $type
 * @property float $amount
 * @property float $balance_before
 * @property float $balance_after
 * @property string $reference_type
 * @property string|null $reference_id
 * @property string|null $from_member_id
 * @property int|null $level
 * @property string|null $description
 */
class WalletTransaction extends Model
{
    use HasFactory, UsesUuid;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'from_member_id',
        'level',
        'description',
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
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'level' => 'integer',
        ];
    }

    /**
     * Get the wallet that owns the transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }

    /**
     * Get the member that this transaction came from (for commissions)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromMember()
    {
        return $this->belongsTo(User::class, 'from_member_id', 'id');
    }
}

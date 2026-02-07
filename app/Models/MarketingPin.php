<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * MarketingPin Model
 * 
 * Represents marketing PIN codes for member registration
 * Each PIN can only be used once (1 PIN = 1 registration)
 * 
 * @package App\Models
 * 
 * @property string $id UUID
 * @property string $code Format: sedXXXX (4 random chars)
 * @property string $admin_id
 * @property string|null $designated_member_id
 * @property string|null $redeemed_by_member_id
 * @property string $status active|used|expired
 * @property \Carbon\Carbon|null $expired_at
 * @property \Carbon\Carbon|null $used_at
 */
class MarketingPin extends Model
{
    use HasFactory, UsesUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'admin_id',
        'designated_member_id',
        'redeemed_by_member_id',
        'status',
        'expired_at',
        'used_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /**
     * Get the admin who generated the PIN
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    /**
     * Get the designated member (tracking purpose only)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designatedMember()
    {
        return $this->belongsTo(User::class, 'designated_member_id', 'id');
    }

    /**
     * Get the member who redeemed this PIN
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function redeemedByMember()
    {
        return $this->belongsTo(User::class, 'redeemed_by_member_id', 'id');
    }

    /**
     * Generate unique PIN code
     * Format: sedXXXX where XXXX is 4 random alphanumeric chars
     *
     * @return string
     */
    public static function generateCode(): string
    {
        do {
            // Generate 4 random alphanumeric characters (uppercase)
            $random = strtoupper(Str::random(4));
            $code = 'SED' . $random;
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check if PIN is valid for use
     * Valid = status is active AND not expired
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expired_at && $this->expired_at->isPast()) {
            // Auto-update status to expired
            $this->update(['status' => 'expired']);
            return false;
        }

        return true;
    }

    /**
     * Mark PIN as used
     *
     * @param string $memberId
     * @return void
     */
    public function markAsUsed(string $memberId): void
    {
        $this->update([
            'status' => 'used',
            'redeemed_by_member_id' => $memberId,
            'used_at' => now(),
        ]);
    }
}

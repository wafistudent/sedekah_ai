<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PinTransaction Model
 * 
 * Represents PIN purchase, transfer, and redemption transactions
 * 
 * @package App\Models
 * 
 * @property string $id UUID
 * @property string $member_id
 * @property string $type
 * @property string|null $target_id
 * @property int $point
 * @property int $before_point
 * @property int $after_point
 * @property string $status
 * @property string|null $description
 */
class PinTransaction extends Model
{
    use HasFactory, UsesUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'type',
        'target_id',
        'point',
        'before_point',
        'after_point',
        'status',
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
            'point' => 'integer',
            'before_point' => 'integer',
            'after_point' => 'integer',
        ];
    }

    /**
     * Get the member user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id', 'id');
    }

    /**
     * Get the target user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function target()
    {
        return $this->belongsTo(User::class, 'target_id', 'id');
    }
}

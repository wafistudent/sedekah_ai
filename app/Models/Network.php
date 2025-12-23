<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Network Model
 * 
 * Represents the MLM network hierarchy with flexible upline placement
 * 
 * @package App\Models
 * 
 * @property string $id UUID
 * @property string $member_id
 * @property string|null $sponsor_id Who registered the member
 * @property string|null $upline_id Position in network tree
 * @property bool $is_marketing Marketing member flag
 * @property string $status
 */
class Network extends Model
{
    use HasFactory, UsesUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'network';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'sponsor_id',
        'upline_id',
        'is_marketing',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_marketing' => 'boolean',
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
     * Get the sponsor user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id', 'id');
    }

    /**
     * Get the upline user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function upline()
    {
        return $this->belongsTo(User::class, 'upline_id', 'id');
    }

    /**
     * Get all downline members under this member
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downlines()
    {
        return $this->hasMany(Network::class, 'upline_id', 'member_id');
    }
}

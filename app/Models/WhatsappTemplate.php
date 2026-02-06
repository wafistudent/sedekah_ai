<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * WhatsappTemplate Model
 * 
 * Represents a WhatsApp message template with variables
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $category
 * @property string|null $subject
 * @property string $content
 * @property array|null $variables
 * @property bool $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class WhatsappTemplate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'category',
        'subject',
        'content',
        'variables',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created this template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the logs for this template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WhatsappLog::class, 'template_id');
    }
}

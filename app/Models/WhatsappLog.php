<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WhatsappLog Model
 * 
 * Represents a WhatsApp message log (audit trail)
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property int|null $template_id
 * @property string $recipient_phone
 * @property string|null $recipient_name
 * @property string $message_content
 * @property string $status
 * @property string|null $error_message
 * @property int $retry_count
 * @property int $max_retry
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property array|null $metadata
 * @property bool $is_manual_resend
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class WhatsappLog extends Model
{
    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_QUEUED = 'queued';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'template_id',
        'recipient_phone',
        'recipient_name',
        'message_content',
        'status',
        'error_message',
        'retry_count',
        'max_retry',
        'sent_at',
        'scheduled_at',
        'metadata',
        'is_manual_resend',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_manual_resend' => 'boolean',
        'retry_count' => 'integer',
        'max_retry' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the template for this log.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsappTemplate::class, 'template_id');
    }
}

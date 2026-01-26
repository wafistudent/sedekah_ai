<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Material Model
 * 
 * Represents learning materials (PDF or YouTube videos) in the MLM system
 * 
 * @package App\Models
 * 
 * @property string $id UUID
 * @property string $title
 * @property string|null $description
 * @property string $type
 * @property string $content
 * @property string $access_type
 * @property int $order
 */
class Material extends Model
{
    use HasFactory, UsesUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'type',
        'content',
        'access_type',
        'order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    /**
     * Get users who completed this material
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function completedBy()
    {
        return $this->belongsToMany(User::class, 'material_completions', 'material_id', 'user_id')
            ->withPivot('completed_at');
    }

    /**
     * Check if material is completed by user
     *
     * @param string $userId
     * @return bool
     */
    public function isCompletedBy(string $userId): bool
    {
        return $this->completedBy()->where('user_id', $userId)->exists();
    }

    /**
     * Get YouTube embed URL with parameters
     *
     * @return string|null
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if ($this->type !== 'youtube') {
            return null;
        }

        // Extract video ID from various YouTube URL formats
        $videoId = $this->extractYoutubeId($this->content);
        
        if (!$videoId) {
            return null;
        }

        return "https://www.youtube.com/embed/{$videoId}?rel=0&modestbranding=1&showinfo=0&controls=1&disablekb=1";
    }

    /**
     * Get PDF URL
     *
     * @return string|null
     */
    public function getPdfUrlAttribute(): ?string
    {
        if ($this->type !== 'pdf') {
            return null;
        }

        return asset('storage/' . $this->content);
    }

    /**
     * Extract YouTube video ID from URL
     *
     * @param string $url
     * @return string|null
     */
    private function extractYoutubeId(string $url): ?string
    {
        // Handle various YouTube URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Scope to filter materials by user's marketing status
     *
     * @param Builder $query
     * @param string $userId
     * @return Builder
     */
    public function scopeAccessibleBy(Builder $query, string $userId): Builder
    {
        $user = User::find($userId);
        
        if (!$user) {
            return $query->where('id', null); // Return empty result
        }

        $network = $user->network;
        
        if (!$network) {
            // User has no network, only show 'all' materials
            return $query->where('access_type', 'all');
        }

        $isMarketing = $network->is_marketing;

        if ($isMarketing) {
            // Marketing members can see 'all' and 'marketing_only'
            return $query->whereIn('access_type', ['all', 'marketing_only']);
        } else {
            // Non-marketing members can see 'all' and 'non_marketing_only'
            return $query->whereIn('access_type', ['all', 'non_marketing_only']);
        }
    }

    /**
     * Scope to order materials
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }
}

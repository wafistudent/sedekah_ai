<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait UsesUuid
 * 
 * Automatically generates UUID for model primary key on creation
 * 
 * @package App\Traits
 */
trait UsesUuid
{
    /**
     * Boot the UsesUuid trait
     * 
     * @return void
     */
    protected static function bootUsesUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}

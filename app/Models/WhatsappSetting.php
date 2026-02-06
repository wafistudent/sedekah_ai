<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * WhatsappSetting Model
 * 
 * Represents WhatsApp system settings with type casting
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string $type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class WhatsappSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get setting value by key with type casting
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        // Cast value based on type
        return match($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set setting value with type
     * 
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    public static function setValue(string $key, $value, string $type = 'text'): void
    {
        // Convert value to string based on type
        if ($type === 'json') {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? 'true' : 'false';
        }
        
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}

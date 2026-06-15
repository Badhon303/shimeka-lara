<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    protected $casts = [
        'value' => 'string',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        return match ($setting->type) {
            'number' => (float) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function set(string $key, $value, string $type = 'string', string $group = 'general', string $label = null)
    {
        $storedValue = match ($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'type' => $type, 'group' => $group, 'label' => $label]
        );
    }

    public static function allByGroup()
    {
        return static::all()->groupBy('group')->map(function ($items) {
            return $items->mapWithKeys(function ($item) {
                $value = match ($item->type) {
                    'number' => (float) $item->value,
                    'boolean' => filter_var($item->value, FILTER_VALIDATE_BOOLEAN),
                    'json' => json_decode($item->value, true),
                    default => $item->value,
                };
                return [$item->key => $value];
            });
        });
    }
}

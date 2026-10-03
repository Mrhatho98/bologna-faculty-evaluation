<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        $val = $setting->value;
        $decoded = json_decode($val, true);
        return (json_last_error() === JSON_ERROR_NONE && !is_numeric($val)) ? $decoded : $val;
    }

    public static function set(string $key, mixed $value, string $group = 'general', ?string $description = null): self
    {
        $stringValue = is_array($value) || is_object($value) ? json_encode($value) : (string)$value;

        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'group' => $group,
                'description' => $description,
            ]
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            'encrypted' => $setting->value ? Crypt::decryptString($setting->value) : '',
            default => $setting->value,
        };
    }

    public static function set(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return;
        }

        $stored = match ($setting->type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            'encrypted' => $value ? Crypt::encryptString($value) : '',
            default => (string) $value,
        };

        $setting->update(['value' => $stored]);
    }
}

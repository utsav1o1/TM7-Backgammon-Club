<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        if ($setting->value === '0' || $setting->value === 'false' || $setting->value === '') {
            return false;
        }
        if ($setting->value === '1' || $setting->value === 'true') {
            return true;
        }

        return $setting->value;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}

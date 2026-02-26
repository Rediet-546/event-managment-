<?php

namespace Modules\Attendee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AttendeeSetting extends Model
{
    protected $table = 'attendee_settings';
    
    protected $fillable = ['key', 'value', 'type', 'group'];
    
    protected $casts = [
        'value' => 'json',
    ];
    
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('attendee_settings');
        });
        
        static::deleted(function () {
            Cache::forget('attendee_settings');
        });
    }
    
    public static function get($key, $default = null)
    {
        return Cache::rememberForever('attendee_settings', function () {
            return self::pluck('value', 'key')->toArray();
        })[$key] ?? $default;
    }
    
    public static function set($key, $value, $type = 'text', $group = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }
}
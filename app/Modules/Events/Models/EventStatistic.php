<?php

namespace App\Modules\Events\Models;

use App\Modules\Core\Base\BaseModel;
use App\Models\User;

class EventStatistic extends BaseModel
{
    protected $table = 'event_statistics';
    
    protected $fillable = [
        'event_id', 'user_id', 'type', 'ip_address', 'user_agent', 'data'
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
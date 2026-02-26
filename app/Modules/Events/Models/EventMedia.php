<?php

namespace App\Modules\Events\Models;

use App\Modules\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EventMedia extends BaseModel
{
    protected $table = 'event_media';
    
    protected $fillable = [
        'event_id', 'type', 'path', 'thumbnail_path', 'title',
        'description', 'size', 'mime_type', 'is_primary', 'sort_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
        'size' => 'integer',
    ];

    protected $appends = ['url', 'thumbnail_url'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
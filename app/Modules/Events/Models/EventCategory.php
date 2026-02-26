<?php

namespace App\Modules\Events\Models;

use App\Modules\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class EventCategory extends BaseModel
{
    use HasSlug;

    protected $table = 'event_categories';
    
    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon', 
        'is_active', 'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
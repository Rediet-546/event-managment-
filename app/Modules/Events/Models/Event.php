<?php

namespace App\Modules\Events\Models;

use App\Modules\Core\Base\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends BaseModel
{
    use HasSlug, HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'category_id', 'user_id', 'title', 'slug', 'description',
        'short_description', 'venue', 'address', 'city', 'country',
        'latitude', 'longitude', 'start_date', 'end_date',
        'registration_deadline', 'max_attendees', 'current_attendees',
        'price', 'currency', 'is_free', 'status', 'is_featured',
        'is_virtual', 'virtual_link', 'meta_data', 'views',
        // NEW FIELDS
        'publish_at', 'published_at', 'cancelled_at', 'cancellation_reason'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'is_free' => 'boolean',
        'is_featured' => 'boolean',
        'is_virtual' => 'boolean',
        'meta_data' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'price' => 'decimal:2',
        'max_attendees' => 'integer',
        'current_attendees' => 'integer',
        'views' => 'integer',
        // NEW CASTS
        'publish_at' => 'datetime',
        'published_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $appends = [
        'is_upcoming', 'is_past', 'is_full', 'available_spots',
        'formatted_price', 'duration', 'status_badge', 'publish_status'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class , 'category_id');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(EventMedia::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function waitlist(): HasMany
    {
        return $this->hasMany(Waitlist::class);
    }

    public function primaryMedia()
    {
        return $this->hasOne(EventMedia::class)->where('is_primary', true);
    }

    // Accessors
    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getIsFullAttribute(): bool
    {
        if (!$this->max_attendees) {
            return false;
        }
        return $this->current_attendees >= $this->max_attendees;
    }

    public function getAvailableSpotsAttribute(): ?int
    {
        if (!$this->max_attendees) {
            return null;
        }
        return max(0, $this->max_attendees - $this->current_attendees);
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free) {
            return 'Free';
        }
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getDurationAttribute(): string
    {
        return $this->start_date->diffForHumans($this->end_date, true);
    }

    /**
     * Get status badge color/class
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
                'published' => ['color' => 'green', 'label' => 'Published'],
                'draft' => ['color' => 'gray', 'label' => 'Draft'],
                'cancelled' => ['color' => 'red', 'label' => 'Cancelled'],
                'completed' => ['color' => 'blue', 'label' => 'Completed'],
                default => ['color' => 'gray', 'label' => ucfirst($this->status)],
            };
    }

    /**
     * Get publish status for scheduled events
     */
    public function getPublishStatusAttribute(): string
    {
        if ($this->status === 'published') {
            return 'Published ' . ($this->published_at ? $this->published_at->diffForHumans() : '');
        }

        if ($this->publish_at && $this->publish_at->isFuture()) {
            return 'Scheduled for ' . $this->publish_at->format('M d, Y g:i A');
        }

        if ($this->status === 'draft') {
            return 'Draft';
        }

        return ucfirst($this->status);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
            ->orderBy('start_date');
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now())
            ->orderBy('start_date', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('publish_at')
            ->where('publish_at', '>', now());
    }

    public function scopePendingPublication($query)
    {
        return $query->where('status', 'draft')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now());
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'LIKE', "%{$city}%");
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('start_date', [$start, $end]);
    }

    public function scopeWithAvailableSpots($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_attendees')
                ->orWhereColumn('current_attendees', '<', 'max_attendees');
        });
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function isBookable(): bool
    {
        return $this->status === 'published'
            && $this->start_date->isFuture()
            && !$this->isFull;
    }

    public function isScheduled(): bool
    {
        return $this->publish_at && $this->publish_at->isFuture();
    }

    public function canBePublished(): bool
    {
        return $this->status === 'draft' && !$this->isScheduled();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['published', 'draft']);
    }
}
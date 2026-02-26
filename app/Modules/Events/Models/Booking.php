<?php

namespace App\Modules\Events\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'event_id',
        'user_id',
        'booking_reference',
        'tickets_count',
        'amount_paid',
        'payment_method',
        'status',
        'booking_date',
        'checked_in_at',
        'cancelled_at',
        'cancellation_reason',
        'meta_data',
    ];

    protected $casts = [
        'tickets_count' => 'integer',
        'amount_paid' => 'decimal:2',
        'booking_date' => 'datetime',
        'checked_in_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'meta_data' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(BookingGuest::class);
    }
}


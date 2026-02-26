<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'user_id',
        'customer_email',
        'customer_name',
        'customer_phone',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency',
        'status',
        'payment_status',
        'notes',
        'metadata',
        'expires_at',
        'confirmed_at',
        'cancelled_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'json',
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(AttendeeTicket::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasMany(AttendeePayment::class, 'booking_id');
    }

    public function history()
    {
        return $this->hasMany(AttendeeBookingHistory::class, 'booking_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2) . ' ' . $this->currency;
    }
}
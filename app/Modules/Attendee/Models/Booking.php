<?php

namespace Modules\Attendee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;
    
    protected $table = 'attendee_bookings';
    
    protected $fillable = [
        'booking_number', 'event_id', 'user_id', 'ticket_type_id',
        'quantity', 'unit_price', 'total_price', 'discount_amount',
        'tax_amount', 'fee_amount', 'final_price', 'status',
        'payment_status', 'payment_method', 'payment_id',
        'booking_date', 'expiry_date', 'checked_in_at', 'checked_in_by',
        'special_requests', 'notes', 'metadata'
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'total_price' => 'float',
        'discount_amount' => 'float',
        'tax_amount' => 'float',
        'fee_amount' => 'float',
        'final_price' => 'float',
        'booking_date' => 'datetime',
        'expiry_date' => 'datetime',
        'checked_in_at' => 'datetime',
        'metadata' => 'array'
    ];
    
    protected $appends = ['status_label', 'can_be_cancelled'];
    
    protected static function booted()
    {
        static::creating(function ($booking) {
            $booking->booking_number = self::generateBookingNumber();
            $booking->booking_date = now();
        });
    }
    
    public static function generateBookingNumber()
    {
        $prefix = 'BKG';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        
        return $prefix . $year . $month . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
    
    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }
    
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
    
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    
    public function checkIn()
    {
        return $this->hasOne(CheckIn::class);
    }
    
    public function history()
    {
        return $this->hasMany(BookingHistory::class);
    }
    
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => ['class' => 'warning', 'text' => 'Pending'],
            'confirmed' => ['class' => 'success', 'text' => 'Confirmed'],
            'cancelled' => ['class' => 'danger', 'text' => 'Cancelled'],
            'refunded' => ['class' => 'info', 'text' => 'Refunded'],
            'expired' => ['class' => 'secondary', 'text' => 'Expired']
        ];
        
        $label = $labels[$this->status] ?? ['class' => 'secondary', 'text' => ucfirst($this->status)];
        
        return '<span class="badge badge-' . $label['class'] . '">' . $label['text'] . '</span>';
    }
    
    public function getCanBeCancelledAttribute()
    {
        return $this->status === 'confirmed' && 
               $this->event && 
               $this->event->start_date->isFuture();
    }
    
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);
        
        $this->generateTickets();
        
        return $this;
    }
    
    public function cancel($reason = null)
    {
        $this->update(['status' => 'cancelled']);
        
        $this->history()->create([
            'action' => 'cancelled',
            'user_id' => auth()->id(),
            'description' => $reason ?? 'Booking cancelled'
        ]);
        
        return $this;
    }
    
    public function generateTickets()
    {
        for ($i = 0; $i < $this->quantity; $i++) {
            $this->tickets()->create([
                'ticket_number' => Ticket::generateTicketNumber(),
                'qr_code' => Ticket::generateQrCode(),
                'attendee_name' => $this->user->name,
                'attendee_email' => $this->user->email,
                'status' => 'active'
            ]);
        }
        
        return $this;
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
    
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }
    
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
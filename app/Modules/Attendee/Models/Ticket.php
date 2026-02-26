<?php

namespace Modules\Attendee\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'attendee_tickets';
    
    protected $fillable = [
        'booking_id', 'ticket_number', 'qr_code', 'attendee_name',
        'attendee_email', 'attendee_phone', 'status', 'checked_in_at',
        'checked_in_by', 'metadata'
    ];
    
    protected $casts = [
        'checked_in_at' => 'datetime',
        'metadata' => 'array'
    ];
    
    protected $appends = ['qr_code_url', 'check_in_url'];
    
    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->ticket_number = self::generateTicketNumber();
            $ticket->qr_code = self::generateQrCode();
        });
    }
    
    public static function generateTicketNumber()
    {
        $prefix = 'TIC';
        return $prefix . time() . rand(1000, 9999);
    }
    
    public static function generateQrCode()
    {
        return 'QR' . md5(uniqid() . time());
    }
    
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    
    public function checkIn()
    {
        return $this->hasOne(CheckIn::class);
    }
    
    public function getQrCodeUrlAttribute()
    {
        return url('/api/attendee/tickets/' . $this->ticket_number . '/qr');
    }
    
    public function getCheckInUrlAttribute()
    {
        return url('/api/attendee/checkin/scan/' . $this->ticket_number);
    }
    
    public function markAsCheckedIn($userId = null)
    {
        $this->update([
            'checked_in_at' => now(),
            'checked_in_by' => $userId,
            'status' => 'used'
        ]);
        
        return $this;
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }
}
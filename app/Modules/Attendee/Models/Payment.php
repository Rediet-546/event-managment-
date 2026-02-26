<?php

namespace Modules\Attendee\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'attendee_payments';
    
    protected $fillable = [
        'booking_id', 'transaction_id', 'amount', 'currency',
        'payment_method', 'status', 'payment_details', 'paid_at'
    ];
    
    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
        'payment_details' => 'array'
    ];
    
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now()
        ]);
        
        return $this;
    }
}
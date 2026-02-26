<?php

namespace Modules\Attendee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketType extends Model
{
    use SoftDeletes;
    
    protected $table = 'attendee_ticket_types';
    
    protected $fillable = [
        'name', 'description', 'price', 'quantity_available',
        'max_per_order', 'min_per_order', 'sale_start_date',
        'sale_end_date', 'status', 'metadata'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'quantity_available' => 'integer',
        'max_per_order' => 'integer',
        'min_per_order' => 'integer',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'metadata' => 'array'
    ];
    
    protected $appends = ['available_quantity', 'is_on_sale'];
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function getAvailableQuantityAttribute()
    {
        if (!$this->quantity_available) {
            return null;
        }
        
        $sold = $this->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->sum('quantity');
            
        return max(0, $this->quantity_available - $sold);
    }
    
    public function getIsOnSaleAttribute()
    {
        $now = now();
        
        if ($this->sale_start_date && $now < $this->sale_start_date) {
            return false;
        }
        
        if ($this->sale_end_date && $now > $this->sale_end_date) {
            return false;
        }
        
        return $this->status === 'active';
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeOnSale($query)
    {
        $now = now();
        
        return $query->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('sale_start_date')
                  ->orWhere('sale_start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('sale_end_date')
                  ->orWhere('sale_end_date', '>=', $now);
            });
    }
}
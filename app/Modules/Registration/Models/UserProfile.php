<?php

namespace App\Modules\Registration\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'profile_photo',
        'bio',
        'preferences',
        'social_links'
    ];

    protected $casts = [
        'preferences' => 'array',
        'social_links' => 'array'
    ];

    /**
     * Relationship: Profile belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo 
            ? asset('storage/' . $this->profile_photo)
            : asset('images/default-avatar.png');
    }
}
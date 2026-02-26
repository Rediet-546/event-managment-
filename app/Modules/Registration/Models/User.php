<?php

namespace App\Modules\Registration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'age',
        'user_type', // 'attendee' or 'event_creator'
        'organization_name',
        'phone',
        'tax_id',
        'is_approved', // for event creators (requires admin approval)
        'approved_at',
        'approved_by',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'last_login_ip'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'approved_at' => 'datetime',
        'age' => 'integer',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'full_name',
        'is_verified',
        'account_age_days',
        'dashboard_url'
    ];

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        $full = trim(trim((string) $this->first_name) . ' ' . trim((string) $this->last_name));
        return $full !== '' ? $full : (string) ($this->name ?? '');
    }

    /**
     * Check if email is verified
     */
    public function getIsVerifiedAttribute(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get account age in days
     */
    public function getAccountAgeDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get dashboard URL based on user type
     */
    public function getDashboardUrlAttribute(): string
    {
        if ($this->user_type === 'event_creator' && $this->is_approved) {
            return route('creator.dashboard');
        }
        return route('attendee.dashboard');
    }

    /**
     * Check if user is an attendee
     */
    public function isAttendee(): bool
    {
        return $this->user_type === 'attendee';
    }

    /**
     * Check if user is an event creator
     */
    public function isEventCreator(): bool
    {
        return $this->user_type === 'event_creator';
    }

    /**
     * Check if event creator is approved
     */
    public function isApprovedCreator(): bool
    {
        return $this->isEventCreator() && $this->is_approved === true;
    }

    /**
     * Check if user can create events
     */
    public function canCreateEvents(): bool
    {
        return $this->isApprovedCreator() || $this->hasRole('super-admin') || $this->hasRole('admin');
    }

    /**
     * Relationships
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function bookings()
    {
        return $this->hasMany(\App\Modules\Attendee\Models\Booking::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Modules\Attendee\Models\Payment::class);
    }

    public function createdEvents()
    {
        return $this->hasMany(\App\Modules\Events\Models\Event::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Update last login information
     */
    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip()
        ]);
    }

    /**
     * Approve event creator (by super admin)
     */
    public function approve($adminId): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $adminId
        ]);
        
        $this->assignRole('event_creator');
        
        activity()
            ->performedOn($this)
            ->withProperties(['approved_by' => $adminId])
            ->log('Event creator approved');
    }

    /**
     * Reject event creator
     */
    public function reject(): void
    {
        $this->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null
        ]);
    }

    /**
     * Scopes
     */
    public function scopeAttendees($query)
    {
        return $query->where('user_type', 'attendee');
    }

    public function scopeEventCreators($query)
    {
        return $query->where('user_type', 'event_creator');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('user_type', 'event_creator')
                     ->where('is_approved', false);
    }

    public function scopeApprovedCreators($query)
    {
        return $query->where('user_type', 'event_creator')
                     ->where('is_approved', true);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->name) && (!empty($user->first_name) || !empty($user->last_name))) {
                $user->name = trim(trim((string) $user->first_name) . ' ' . trim((string) $user->last_name));
            }

            if (empty($user->username)) {
                $user->username = self::generateUniqueUsername($user->first_name, $user->last_name);
            }
        });

        static::created(function ($user) {
            $user->profile()->create([]);
            
            // Assign default role based on user type
            if ($user->user_type === 'attendee') {
                $user->assignRole('attendee');
            }
            // Event creators get role only after approval
            
            activity()
                ->performedOn($user)
                ->withProperties(['user_type' => $user->user_type])
                ->log('User account created');
        });
    }

    private static function generateUniqueUsername($firstName, $lastName)
    {
        $firstName = (string) ($firstName ?? '');
        $lastName = (string) ($lastName ?? '');

        $base = strtolower(trim($firstName . '.' . $lastName, '.'));
        if ($base === '') {
            $base = 'user';
        }
        $username = $base;
        $counter = 1;
        
        while (self::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }
}
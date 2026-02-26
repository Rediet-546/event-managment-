<?php

namespace App\Modules\Events\Policies;

use App\Models\User;
use App\Modules\Events\Models\Event;

class EventPolicy
{
    /**
     * Determine if the user can view any events.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Public can view events
    }

    /**
     * Determine if the user can view the event.
     */
    public function view(?User $user, Event $event): bool
    {
        // Public events are viewable by anyone
        if ($event->status === 'published') {
            return true;
        }

        // Draft/cancelled events only visible to vendor (owner) and admins
        return $user && (
            $user->id === $event->user_id || 
            $user->hasAnyPermission(['manage all events', 'edit events'])
        );
    }

    /**
     * Determine if the user can create events.
     */
    public function create(User $user): bool
    {
        // Check if user has permission AND is an active, approved creator (or admin)
        return $user->hasPermissionTo('create events')
            && $user->is_active
            && (
                $user->hasRole('event_creator')
                || $user->hasRole('admin')
                || $user->hasRole('super-admin')
            );
    }

    /**
     * Determine if the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        // Vendors can edit their own events
        if ($user->id === $event->user_id && $user->hasPermissionTo('edit events')) {
            return true;
        }

        // Admins can edit any event
        return $user->hasPermissionTo('manage all events');
    }

    /**
     * Determine if the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        // Only admins can delete events
        return $user->hasPermissionTo('manage all events');
    }

    /**
     * Determine if the user can publish the event.
     */
    public function publish(User $user, Event $event): bool
    {
        // Creators can publish their own events if approved
        if (
            $user->id === $event->user_id
            && $user->hasPermissionTo('publish events')
            && ($user->approved_at || $user->is_approved)
        ) {
            return true;
        }

        // Admins can publish any event
        return $user->hasPermissionTo('manage all events');
    }

    /**
     * Determine if the user can cancel the event.
     */
    public function cancel(User $user, Event $event): bool
    {
        // Vendors can cancel their own events
        if ($user->id === $event->user_id && $user->hasPermissionTo('cancel events')) {
            return true;
        }

        // Admins can cancel any event
        return $user->hasPermissionTo('manage all events');
    }

    /**
     * Determine if the user can duplicate the event.
     */
    public function duplicate(User $user, Event $event): bool
    {
        // Vendors can duplicate their own events
        if ($user->id === $event->user_id && $user->hasPermissionTo('duplicate events')) {
            return true;
        }

        // Admins can duplicate any event
        return $user->hasPermissionTo('manage all events');
    }

    /**
     * Determine if the user can manage bookings for this event.
     */
    public function manageBookings(User $user, Event $event): bool
    {
        // Event owners can manage bookings for their own events
        if ($user->id === $event->user_id) {
            return true;
        }

        // Admins/privileged users can manage bookings for any event
        return $user->hasPermissionTo('manage all bookings');
    }

    /**
     * Determine if the user can check-in attendees for this event.
     */
    public function checkInAttendees(User $user, Event $event): bool
    {
        // Event owners can check-in attendees for their own events
        if ($user->id === $event->user_id) {
            return true;
        }

        // Admins/privileged users can check-in for any event
        return $user->hasPermissionTo('check-in attendees');
    }
}
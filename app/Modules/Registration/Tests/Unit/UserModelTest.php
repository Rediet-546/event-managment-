<?php

namespace App\Modules\Registration\Tests\Unit;

use Tests\TestCase;
use App\Modules\Registration\Models\User;
use App\Modules\Registration\Models\UserProfile;
use App\Modules\Events\Models\Event;
use App\Modules\Attendee\Models\Booking;
use App\Modules\Attendee\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\Carbon;

class UserModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $attendee;
    protected $creator;
    protected $admin;
    protected $superAdmin;
    protected $pendingCreator;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users with different roles and types
        $this->attendee = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'attendee@test.com',
            'username' => 'johndoe',
            'user_type' => 'attendee',
            'age' => 25,
            'is_active' => true,
            'email_verified_at' => now(),
            'last_login_at' => null,
            'last_login_ip' => null,
            'created_at' => now()->subDays(30)
        ]);

        $this->creator = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'creator@test.com',
            'username' => 'janesmith',
            'user_type' => 'event_creator',
            'organization_name' => 'Jane Events Inc',
            'phone' => '+1234567890',
            'tax_id' => 'TAX123456',
            'is_approved' => true,
            'approved_at' => now()->subDays(5),
            'approved_by' => 1,
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        $this->admin = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@test.com',
            'username' => 'adminuser',
            'user_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $this->admin->assignRole('admin');

        $this->superAdmin = User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super@test.com',
            'username' => 'superadmin',
            'user_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $this->superAdmin->assignRole('super-admin');

        $this->pendingCreator = User::factory()->create([
            'first_name' => 'Pending',
            'last_name' => 'Creator',
            'email' => 'pending@test.com',
            'username' => 'pendingcreator',
            'user_type' => 'event_creator',
            'organization_name' => 'Pending Events',
            'is_approved' => false,
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        // Create profiles for all users
        UserProfile::factory()->create(['user_id' => $this->attendee->id]);
        UserProfile::factory()->create(['user_id' => $this->creator->id]);
        UserProfile::factory()->create(['user_id' => $this->admin->id]);
        UserProfile::factory()->create(['user_id' => $this->superAdmin->id]);
        UserProfile::factory()->create(['user_id' => $this->pendingCreator->id]);
    }

    /** @test */
    public function user_has_full_name_attribute()
    {
        $this->assertEquals('John Doe', $this->attendee->full_name);
        $this->assertEquals('Jane Smith', $this->creator->full_name);
        $this->assertEquals('Admin User', $this->admin->full_name);
    }

    /** @test */
    public function user_has_is_verified_attribute()
    {
        $this->assertTrue($this->attendee->is_verified);
        
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null
        ]);
        
        $this->assertFalse($unverifiedUser->is_verified);
    }

    /** @test */
    public function user_has_account_age_days_attribute()
    {
        $user = User::factory()->create([
            'created_at' => now()->subDays(10)
        ]);
        
        $this->assertEquals(10, $user->account_age_days);
        $this->assertEquals(30, $this->attendee->account_age_days);
    }

    /** @test */
    public function user_has_dashboard_url_attribute()
    {
        $this->assertEquals(route('attendee.dashboard'), $this->attendee->dashboard_url);
        $this->assertEquals(route('creator.dashboard'), $this->creator->dashboard_url);
        $this->assertEquals(route('admin.dashboard'), $this->admin->dashboard_url);
    }

    /** @test */
    public function user_can_be_identified_as_attendee()
    {
        $this->assertTrue($this->attendee->isAttendee());
        $this->assertFalse($this->attendee->isEventCreator());
        $this->assertFalse($this->attendee->isAdmin());
        $this->assertFalse($this->attendee->isSuperAdmin());
        $this->assertFalse($this->attendee->isApprovedCreator());
    }

    /** @test */
    public function user_can_be_identified_as_event_creator()
    {
        $this->assertTrue($this->creator->isEventCreator());
        $this->assertFalse($this->creator->isAttendee());
        $this->assertFalse($this->creator->isAdmin());
        $this->assertFalse($this->creator->isSuperAdmin());
        $this->assertTrue($this->creator->isApprovedCreator());
    }

    /** @test */
    public function user_can_be_identified_as_admin()
    {
        $this->assertTrue($this->admin->isAdmin());
        $this->assertFalse($this->admin->isSuperAdmin());
        $this->assertFalse($this->admin->isAttendee());
        $this->assertFalse($this->admin->isEventCreator());
    }

    /** @test */
    public function user_can_be_identified_as_super_admin()
    {
        $this->assertTrue($this->superAdmin->isSuperAdmin());
        $this->assertTrue($this->superAdmin->isAdmin()); // Super admin is also admin
        $this->assertFalse($this->superAdmin->isAttendee());
        $this->assertFalse($this->superAdmin->isEventCreator());
    }

    /** @test */
    public function pending_creator_is_not_approved()
    {
        $this->assertTrue($this->pendingCreator->isEventCreator());
        $this->assertFalse($this->pendingCreator->isApprovedCreator());
        $this->assertNull($this->pendingCreator->approved_at);
        $this->assertNull($this->pendingCreator->approved_by);
    }

    /** @test */
    public function user_can_check_if_they_can_create_events()
    {
        $this->assertFalse($this->attendee->canCreateEvents());
        $this->assertTrue($this->creator->canCreateEvents());
        $this->assertTrue($this->admin->canCreateEvents());
        $this->assertTrue($this->superAdmin->canCreateEvents());
        $this->assertFalse($this->pendingCreator->canCreateEvents()); // Not approved yet
    }

    /** @test */
    public function user_has_profile_relationship()
    {
        $this->assertInstanceOf(UserProfile::class, $this->attendee->profile);
        $this->assertInstanceOf(UserProfile::class, $this->creator->profile);
        $this->assertInstanceOf(UserProfile::class, $this->admin->profile);
        
        // Test profile attributes
        $this->assertNotNull($this->attendee->profile->id);
        $this->assertEquals($this->attendee->id, $this->attendee->profile->user_id);
    }

    /** @test */
    public function user_has_bookings_relationship()
    {
        // Create events
        $event1 = Event::factory()->create();
        $event2 = Event::factory()->create();
        
        // Create bookings for attendee
        $booking1 = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $event1->id,
            'status' => 'confirmed'
        ]);
        
        $booking2 = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $event2->id,
            'status' => 'pending'
        ]);

        // Refresh relationship
        $this->attendee->load('bookings');

        $this->assertCount(2, $this->attendee->bookings);
        $this->assertInstanceOf(Booking::class, $this->attendee->bookings->first());
        $this->assertEquals('confirmed', $this->attendee->bookings->first()->status);
        
        // Creator should have no bookings (they create events, not book them)
        $this->assertCount(0, $this->creator->bookings);
    }

    /** @test */
    public function user_has_payments_relationship()
    {
        // Create event and booking
        $event = Event::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $event->id
        ]);
        
        // Create payments
        $payment1 = Payment::factory()->create([
            'user_id' => $this->attendee->id,
            'booking_id' => $booking->id,
            'amount' => 100.00,
            'payment_status' => 'completed'
        ]);
        
        $payment2 = Payment::factory()->create([
            'user_id' => $this->attendee->id,
            'booking_id' => $booking->id,
            'amount' => 50.00,
            'payment_status' => 'pending'
        ]);

        $this->assertCount(2, $this->attendee->payments);
        $this->assertInstanceOf(Payment::class, $this->attendee->payments->first());
        $this->assertEquals(150.00, $this->attendee->payments->sum('amount'));
    }

    /** @test */
    public function creator_has_created_events_relationship()
    {
        // Create events for creator
        $event1 = Event::factory()->create([
            'creator_id' => $this->creator->id,
            'title' => 'Creator Event 1'
        ]);
        
        $event2 = Event::factory()->create([
            'creator_id' => $this->creator->id,
            'title' => 'Creator Event 2'
        ]);
        
        $event3 = Event::factory()->create([
            'creator_id' => $this->creator->id,
            'title' => 'Creator Event 3'
        ]);

        $this->assertCount(3, $this->creator->createdEvents);
        $this->assertInstanceOf(Event::class, $this->creator->createdEvents->first());
        $this->assertEquals('Creator Event 1', $this->creator->createdEvents->first()->title);
        
        // Attendee should have no created events
        $this->assertCount(0, $this->attendee->createdEvents);
    }

    /** @test */
    public function creator_can_get_total_revenue_from_events()
    {
        // Create events with bookings
        $event1 = Event::factory()->create(['creator_id' => $this->creator->id]);
        $event2 = Event::factory()->create(['creator_id' => $this->creator->id]);
        
        // Create bookings with payments
        $booking1 = Booking::factory()->create([
            'event_id' => $event1->id,
            'total_amount' => 200.00,
            'status' => 'confirmed'
        ]);
        
        Payment::factory()->create([
            'booking_id' => $booking1->id,
            'amount' => 200.00,
            'payment_status' => 'completed'
        ]);
        
        $booking2 = Booking::factory()->create([
            'event_id' => $event2->id,
            'total_amount' => 150.00,
            'status' => 'confirmed'
        ]);
        
        Payment::factory()->create([
            'booking_id' => $booking2->id,
            'amount' => 150.00,
            'payment_status' => 'completed'
        ]);

        $totalRevenue = $this->creator->createdEvents()
            ->withSum('bookings', 'total_amount')
            ->get()
            ->sum('bookings_sum_total_amount');

        $this->assertEquals(350.00, $totalRevenue);
    }

    /** @test */
    public function user_can_be_approved_as_creator()
    {
        $this->assertFalse($this->pendingCreator->is_approved);
        $this->assertNull($this->pendingCreator->approved_at);
        $this->assertNull($this->pendingCreator->approved_by);

        $this->pendingCreator->approve(1);

        $this->assertTrue($this->pendingCreator->fresh()->is_approved);
        $this->assertNotNull($this->pendingCreator->fresh()->approved_at);
        $this->assertEquals(1, $this->pendingCreator->fresh()->approved_by);
        
        // Should now be able to create events
        $this->assertTrue($this->pendingCreator->fresh()->canCreateEvents());
    }

    /** @test */
    public function user_can_be_rejected_as_creator()
    {
        $this->pendingCreator->reject();

        $this->assertFalse($this->pendingCreator->fresh()->is_approved);
        $this->assertNull($this->pendingCreator->fresh()->approved_at);
        $this->assertNull($this->pendingCreator->fresh()->approved_by);
    }

    /** @test */
    public function last_login_can_be_updated()
    {
        $this->assertNull($this->attendee->last_login_at);
        $this->assertNull($this->attendee->last_login_ip);

        // Mock request IP
        request()->server->set('REMOTE_ADDR', '192.168.1.1');
        
        $this->attendee->updateLastLogin();

        $this->assertNotNull($this->attendee->fresh()->last_login_at);
        $this->assertEquals('192.168.1.1', $this->attendee->fresh()->last_login_ip);
        $this->assertInstanceOf(Carbon::class, $this->attendee->fresh()->last_login_at);
    }

    /** @test */
    public function user_has_approver_relationship()
    {
        $this->creator->update([
            'approved_by' => $this->superAdmin->id
        ]);

        $this->assertInstanceOf(User::class, $this->creator->fresh()->approver);
        $this->assertEquals($this->superAdmin->id, $this->creator->approver->id);
        $this->assertEquals('Super Admin', $this->creator->approver->first_name);
    }

    /** @test */
    public function scope_filters_work_correctly()
    {
        // Active scope
        $activeUsers = User::active()->get();
        $this->assertTrue($activeUsers->contains($this->attendee));
        $this->assertTrue($activeUsers->contains($this->creator));
        
        $inactiveUser = User::factory()->create(['is_active' => false]);
        $this->assertFalse($activeUsers->contains($inactiveUser));

        // Attendee scope
        $attendees = User::attendees()->get();
        $this->assertTrue($attendees->contains($this->attendee));
        $this->assertFalse($attendees->contains($this->creator));
        $this->assertFalse($attendees->contains($this->admin));

        // Event creators scope
        $creators = User::eventCreators()->get();
        $this->assertTrue($creators->contains($this->creator));
        $this->assertTrue($creators->contains($this->pendingCreator));
        $this->assertFalse($creators->contains($this->attendee));

        // Pending approval scope
        $pending = User::pendingApproval()->get();
        $this->assertTrue($pending->contains($this->pendingCreator));
        $this->assertFalse($pending->contains($this->creator));

        // Approved creators scope
        $approved = User::approvedCreators()->get();
        $this->assertTrue($approved->contains($this->creator));
        $this->assertFalse($approved->contains($this->pendingCreator));
    }

    /** @test */
    public function age_scope_works_correctly()
    {
        User::factory()->create(['age' => 20]);
        User::factory()->create(['age' => 25]);
        User::factory()->create(['age' => 30]);
        User::factory()->create(['age' => 35]);
        User::factory()->create(['age' => 40]);

        $users1824 = User::ageBetween(18, 24)->get();
        $this->assertEquals(20, $users1824->first()->age);
        
        $users2534 = User::ageBetween(25, 34)->get();
        $this->assertCount(2, $users2534); // 25 and 30
        
        $users3544 = User::ageBetween(35, 44)->get();
        $this->assertCount(2, $users3544); // 35 and 40
    }

    /** @test */
    public function search_scope_works_correctly()
    {
        // Search by first name
        $results = User::where('first_name', 'LIKE', '%Jane%')->get();
        $this->assertTrue($results->contains($this->creator));
        
        // Search by last name
        $results = User::where('last_name', 'LIKE', '%Smith%')->get();
        $this->assertTrue($results->contains($this->creator));
        
        // Search by email
        $results = User::where('email', 'LIKE', '%creator%@test.com%')->get();
        $this->assertTrue($results->contains($this->creator));
        
        // Search by organization (for creators)
        $results = User::where('organization_name', 'LIKE', '%Jane%')->get();
        $this->assertTrue($results->contains($this->creator));
    }

    /** @test */
    public function profile_is_automatically_created_when_user_is_created()
    {
        $user = User::factory()->create();
        
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id
        ]);
        
        $this->assertInstanceOf(UserProfile::class, $user->profile);
    }

    /** @test */
    public function username_is_automatically_generated_if_not_provided()
    {
        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => null // Will be auto-generated by model boot method
        ]);

        $this->assertNotNull($user->username);
        $this->assertEquals('test.user', $user->username);
    }

    /** @test */
    public function username_is_made_unique_if_duplicate()
    {
        // First user
        $user1 = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'john.doe'
        ]);

        // Second user with same name should get unique username
        $user2 = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => null
        ]);

        $this->assertNotEquals($user1->username, $user2->username);
        $this->assertEquals('john.doe1', $user2->username);
    }

    /** @test */
    public function user_can_check_event_booking_eligibility()
    {
        $event = Event::factory()->create([
            'metadata' => ['min_age' => 21]
        ]);

        // Attendee is 25, should be eligible
        $this->assertTrue($this->attendee->canBookEvent($event));

        // Young attendee
        $youngAttendee = User::factory()->create([
            'user_type' => 'attendee',
            'age' => 18
        ]);
        
        $this->assertFalse($youngAttendee->canBookEvent($event));

        // Event with no age restriction
        $eventNoRestriction = Event::factory()->create([
            'metadata' => []
        ]);
        
        $this->assertTrue($youngAttendee->canBookEvent($eventNoRestriction));
    }

    /** @test */
    public function user_can_get_upcoming_bookings()
    {
        $futureEvent = Event::factory()->create([
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(11)
        ]);
        
        $pastEvent = Event::factory()->create([
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(9)
        ]);

        Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $futureEvent->id,
            'status' => 'confirmed'
        ]);
        
        Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $pastEvent->id,
            'status' => 'completed'
        ]);

        $upcomingBookings = $this->attendee->bookings()
            ->whereHas('event', function($query) {
                $query->where('start_date', '>', now());
            })
            ->get();

        $this->assertCount(1, $upcomingBookings);
        $this->assertEquals($futureEvent->id, $upcomingBookings->first()->event_id);
    }

    /** @test */
    public function user_can_get_total_spent()
    {
        $event = Event::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $event->id,
            'total_amount' => 100.00
        ]);
        
        Payment::factory()->create([
            'user_id' => $this->attendee->id,
            'booking_id' => $booking->id,
            'amount' => 100.00,
            'payment_status' => 'completed'
        ]);

        $booking2 = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $event->id,
            'total_amount' => 75.00
        ]);
        
        Payment::factory()->create([
            'user_id' => $this->attendee->id,
            'booking_id' => $booking2->id,
            'amount' => 75.00,
            'payment_status' => 'completed'
        ]);

        $totalSpent = $this->attendee->payments()
            ->where('payment_status', 'completed')
            ->sum('amount');

        $this->assertEquals(175.00, $totalSpent);
    }

    /** @test */
    public function user_model_uses_soft_deletes()
    {
        $user = User::factory()->create();
        $userId = $user->id;
        
        $user->delete();
        
        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertNull(User::find($userId));
        $this->assertNotNull(User::withTrashed()->find($userId));
    }

    /** @test */
    public function user_can_be_restored_from_soft_delete()
    {
        $user = User::factory()->create();
        $userId = $user->id;
        
        $user->delete();
        $this->assertSoftDeleted('users', ['id' => $userId]);
        
        $user->restore();
        $this->assertDatabaseHas('users', ['id' => $userId, 'deleted_at' => null]);
    }

    /** @test */
    public function user_casts_are_correct()
    {
        $this->assertIsBool($this->attendee->is_active);
        $this->assertIsBool($this->attendee->is_approved);
        $this->assertIsInt($this->attendee->age);
        $this->assertInstanceOf(Carbon::class, $this->attendee->created_at);
        $this->assertInstanceOf(Carbon::class, $this->attendee->updated_at);
        $this->assertInstanceOf(Carbon::class, $this->attendee->email_verified_at);
    }

    /** @test */
    public function user_hidden_attributes_are_not_serialized()
    {
        $array = $this->attendee->toArray();
        
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /** @test */
    public function user_fillable_attributes_can_be_mass_assigned()
    {
        $data = [
            'first_name' => 'New',
            'last_name' => 'Name',
            'email' => 'new@test.com',
            'username' => 'newname',
            'password' => Hash::make('password'),
            'user_type' => 'attendee',
            'age' => 30
        ];

        $user = User::create($data);

        foreach ($data as $key => $value) {
            if ($key !== 'password') {
                $this->assertEquals($value, $user->$key);
            }
        }
    }

    /** @test */
    public function user_activity_is_logged_on_creation()
    {
        // This test assumes you have activity logging set up
        $user = User::factory()->create();
        
        $this->assertDatabaseHas('activity_log', [
            'causer_id' => $user->id,
            'description' => 'User account created'
        ]);
    }

    /** @test */
    public function user_has_many_notifications()
    {
        $this->attendee->notify(new \Illuminate\Notifications\Notification());
        
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->attendee->id,
            'notifiable_type' => User::class
        ]);
    }

    /** @test */
    public function user_can_have_multiple_roles()
    {
        $this->admin->assignRole('moderator');
        
        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue($this->admin->hasRole('moderator'));
    }

    /** @test */
    public function user_can_have_multiple_permissions()
    {
        $this->admin->givePermissionTo('edit events');
        $this->admin->givePermissionTo('delete events');
        
        $this->assertTrue($this->admin->can('edit events'));
        $this->assertTrue($this->admin->can('delete events'));
    }

    /** @test */
    public function user_route_binding_works_with_soft_deletes()
    {
        $user = User::factory()->create();
        $user->delete();
        
        // This should fail normally
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        User::findOrFail($user->id);
        
        // This should work with trashed
        $found = User::withTrashed()->find($user->id);
        $this->assertNotNull($found);
    }
}
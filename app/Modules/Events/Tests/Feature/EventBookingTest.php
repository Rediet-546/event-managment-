<?php

namespace App\Modules\Events\Tests\Feature;

use Tests\TestCase;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class EventBookingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $organizer;
    protected $category;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->organizer = User::factory()->create();
        $this->category = EventCategory::factory()->create([
            'name' => 'Conference',
            'slug' => 'conference'
        ]);

        $this->event = Event::factory()->create([
            'user_id' => $this->organizer->id,
            'category_id' => $this->category->id,
            'title' => 'Test Event',
            'slug' => 'test-event',
            'status' => 'published',
            'max_attendees' => 100,
            'current_attendees' => 0,
            'price' => 50.00,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(11),
        ]);
    }

    /** @test */
    public function user_can_view_event_booking_page()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('events.book', $this->event->slug));

        $response->assertStatus(200);
        $response->assertViewHas('event');
        $response->assertSee('Book Ticket');
        $response->assertSee($this->event->title);
    }

    /** @test */
    public function user_can_book_an_event()
    {
        $this->actingAs($this->user);

        $bookingData = [
            'tickets' => 2,
            'attendees' => [
                ['name' => 'John Doe', 'email' => 'john@example.com'],
                ['name' => 'Jane Doe', 'email' => 'jane@example.com']
            ],
            'payment_method' => 'stripe',
            'terms_accepted' => true
        ];

        $response = $this->post(route('events.process-booking', $this->event->slug), $bookingData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert booking was created
        $this->assertDatabaseHas('bookings', [
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'tickets_count' => 2,
            'status' => 'confirmed'
        ]);

        // Assert event attendees count was incremented
        $this->assertEquals(2, $this->event->fresh()->current_attendees);
    }

    /** @test */
    public function user_cannot_book_more_than_available_tickets()
    {
        $this->actingAs($this->user);

        // Set current attendees to 95
        $this->event->update(['current_attendees' => 95]);

        $response = $this->post(route('events.process-booking', $this->event->slug), [
            'tickets' => 10, // Only 5 spots available
            'attendees' => $this->generateAttendees(10),
            'payment_method' => 'stripe',
            'terms_accepted' => true
        ]);

        $response->assertSessionHasErrors(['tickets']);
        $this->assertStringContainsString('only', session('errors')->first('tickets'));
    }

    /** @test */
    public function user_cannot_book_cancelled_or_draft_events()
    {
        $this->actingAs($this->user);

        // Test cancelled event
        $this->event->update(['status' => 'cancelled']);
        
        $response = $this->post(route('events.process-booking', $this->event->slug), [
            'tickets' => 1,
            'attendees' => $this->generateAttendees(1),
            'payment_method' => 'stripe',
            'terms_accepted' => true
        ]);

        $response->assertStatus(403);

        // Test draft event
        $this->event->update(['status' => 'draft']);
        
        $response = $this->post(route('events.process-booking', $this->event->slug), [
            'tickets' => 1,
            'attendees' => $this->generateAttendees(1),
            'payment_method' => 'stripe',
            'terms_accepted' => true
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_book_past_events()
    {
        $this->actingAs($this->user);

        $this->event->update([
            'start_date' => now()->subDays(1),
            'end_date' => now()->subDays(1)->addHours(2)
        ]);

        $response = $this->post(route('events.process-booking', $this->event->slug), [
            'tickets' => 1,
            'attendees' => $this->generateAttendees(1),
            'payment_method' => 'stripe',
            'terms_accepted' => true
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_cancel_their_booking()
    {
        $this->actingAs($this->user);

        // First create a booking
        $booking = $this->createBooking();

        $response = $this->post(route('events.cancel-booking', ['event' => $this->event->slug, 'booking' => $booking->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert booking was cancelled
        $this->assertEquals('cancelled', $booking->fresh()->status);

        // Assert event attendees count was decremented
        $this->assertEquals(0, $this->event->fresh()->current_attendees);
    }

    /** @test */
    public function user_can_view_their_bookings()
    {
        $this->actingAs($this->user);

        // Create multiple bookings
        $this->createBooking();
        $this->createBooking();

        $response = $this->get(route('events.my-bookings'));

        $response->assertStatus(200);
        $response->assertViewHas('bookings');
        $this->assertCount(2, $response->viewData('bookings'));
    }

    /** @test */
    public function event_organizer_can_view_all_bookings()
    {
        $this->actingAs($this->organizer);

        // Create bookings from different users
        $this->createBooking(); // User 1
        $this->createBooking(User::factory()->create()); // User 2
        $this->createBooking(User::factory()->create()); // User 3

        $response = $this->get(route('events.manage-bookings', $this->event->slug));

        $response->assertStatus(200);
        $response->assertViewHas('bookings');
        $this->assertCount(3, $response->viewData('bookings'));
    }

    /** @test */
    public function event_organizer_can_check_in_attendees()
    {
        $this->actingAs($this->organizer);

        $booking = $this->createBooking();

        $response = $this->post(route('events.check-in', ['event' => $this->event->slug, 'booking' => $booking->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNotNull($booking->fresh()->checked_in_at);
    }

    /** @test */
    public function validates_attendee_information()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('events.process-booking', $this->event->slug), [
            'tickets' => 2,
            'attendees' => [
                ['name' => '', 'email' => 'invalid-email'],
                ['name' => 'Jane Doe', 'email' => '']
            ],
            'payment_method' => 'stripe',
            'terms_accepted' => true
        ]);

        $response->assertSessionHasErrors([
            'attendees.0.name',
            'attendees.0.email',
            'attendees.1.email'
        ]);
    }

    /** @test */
    public function requires_terms_acceptance()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('events.process-booking', $this->event->slug), [
            'tickets' => 1,
            'attendees' => $this->generateAttendees(1),
            'payment_method' => 'stripe',
            'terms_accepted' => false
        ]);

        $response->assertSessionHasErrors(['terms_accepted']);
    }

    /** @test */
    public function handles_free_event_bookings()
    {
        $this->actingAs($this->user);

        $freeEvent = Event::factory()->create([
            'user_id' => $this->organizer->id,
            'title' => 'Free Event',
            'status' => 'published',
            'price' => 0,
            'is_free' => true,
            'max_attendees' => 50,
            'start_date' => now()->addDays(5),
        ]);

        $response = $this->post(route('events.process-booking', $freeEvent->slug), [
            'tickets' => 3,
            'attendees' => $this->generateAttendees(3),
            'terms_accepted' => true
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'event_id' => $freeEvent->id,
            'user_id' => $this->user->id,
            'tickets_count' => 3,
            'amount_paid' => 0,
            'status' => 'confirmed'
        ]);
    }

    /**
     * Helper method to create a booking.
     */
    protected function createBooking($user = null)
    {
        $user = $user ?? $this->user;
        
        $booking = \App\Modules\Attendee\Models\Booking::create([
            'event_id' => $this->event->id,
            'user_id' => $user->id,
            'booking_reference' => 'BK-' . strtoupper(uniqid()),
            'tickets_count' => 1,
            'amount_paid' => $this->event->price,
            'status' => 'confirmed',
            'booking_date' => now(),
        ]);

        $this->event->increment('current_attendees');

        return $booking;
    }

    /**
     * Helper method to generate attendees.
     */
    protected function generateAttendees($count)
    {
        $attendees = [];
        for ($i = 0; $i < $count; $i++) {
            $attendees[] = [
                'name' => $this->faker->name,
                'email' => $this->faker->email
            ];
        }
        return $attendees;
    }
}
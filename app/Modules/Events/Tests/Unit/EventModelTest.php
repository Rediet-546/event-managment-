<?php

namespace App\Modules\Events\Tests\Unit;

use Tests\TestCase;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_event()
    {
        $category = EventCategory::factory()->create();
        $user = User::factory()->create();

        $event = Event::create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Test Event',
            'description' => 'Test Description',
            'venue' => 'Test Venue',
            'address' => '123 Test St',
            'city' => 'Test City',
            'country' => 'Test Country',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(8),
            'price' => 100,
        ]);

        $this->assertDatabaseHas('events', ['title' => 'Test Event']);
        $this->assertEquals('test-event', $event->slug);
    }

    /** @test */
    public function it_determines_if_event_is_upcoming()
    {
        $event = Event::factory()->make([
            'start_date' => now()->addDays(5),
        ]);

        $this->assertTrue($event->is_upcoming);

        $pastEvent = Event::factory()->make([
            'start_date' => now()->subDays(5),
        ]);

        $this->assertFalse($pastEvent->is_upcoming);
    }

    /** @test */
    public function it_calculates_available_spots_correctly()
    {
        $event = Event::factory()->create([
            'max_attendees' => 100,
            'current_attendees' => 30,
        ]);

        $this->assertEquals(70, $event->available_spots);
        $this->assertFalse($event->is_full);

        $fullEvent = Event::factory()->create([
            'max_attendees' => 50,
            'current_attendees' => 50,
        ]);

        $this->assertEquals(0, $fullEvent->available_spots);
        $this->assertTrue($fullEvent->is_full);
    }
}
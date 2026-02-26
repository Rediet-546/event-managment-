<?php

namespace App\Modules\Events\Tests\Feature;

use Tests\TestCase;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = EventCategory::factory()->create();
    }

    /** @test */
    public function user_can_create_an_event()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('events.store'), [
            'category_id' => $this->category->id,
            'title' => 'New Test Event',
            'description' => 'Event description here',
            'venue' => 'Test Venue',
            'address' => '123 Main St',
            'city' => 'New York',
            'country' => 'USA',
            'start_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(11)->format('Y-m-d H:i:s'),
            'price' => 50.00,
            'currency' => 'USD',
            'max_attendees' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', ['title' => 'New Test Event']);
    }

    /** @test */
    public function user_can_view_an_event()
    {
        $event = Event::factory()->published()->create();

        $response = $this->get(route('events.show', $event->slug));

        $response->assertStatus(200);
        $response->assertSee($event->title);
    }

    /** @test */
    public function user_can_update_their_own_event()
    {
        $this->actingAs($this->user);
        
        $event = Event::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('events.update', $event), [
            'title' => 'Updated Event Title',
            'description' => $event->description,
            'venue' => $event->venue,
            'address' => $event->address,
            'city' => $event->city,
            'country' => $event->country,
            'start_date' => $event->start_date->format('Y-m-d H:i:s'),
            'end_date' => $event->end_date->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', ['title' => 'Updated Event Title']);
    }

    /** @test */
    public function user_cannot_update_someone_elses_event()
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        
        $event = Event::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('events.update', $event), [
            'title' => 'Hacked Event Title',
        ]);

        $response->assertStatus(403);
    }
}
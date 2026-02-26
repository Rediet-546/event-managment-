<?php

namespace Database\Factories;

use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 48) . ' hours');
        $isFree = $this->faker->boolean(30);
        
        return [
            'category_id' => EventCategory::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'slug' => fn(array $attributes) => Str::slug($attributes['title']),
            'description' => $this->faker->paragraphs(5, true),
            'short_description' => $this->faker->paragraph(2),
            'venue' => $this->faker->company,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'country' => $this->faker->country,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_deadline' => (clone $startDate)->modify('-1 day'),
            'max_attendees' => $this->faker->optional(0.7)->numberBetween(10, 500),
            'current_attendees' => 0,
            'price' => $isFree ? 0 : $this->faker->randomFloat(2, 10, 500),
            'currency' => 'USD',
            'is_free' => $isFree,
            'status' => $this->faker->randomElement(['draft', 'published', 'cancelled', 'completed']),
            'is_featured' => $this->faker->boolean(20),
            'is_virtual' => $this->faker->boolean(30),
            'virtual_link' => fn(array $attributes) => $attributes['is_virtual'] ? $this->faker->url : null,
            'meta_data' => [
                'seo_title' => $this->faker->sentence,
                'seo_description' => $this->faker->paragraph,
                'tags' => $this->faker->words(5),
            ],
            'views' => $this->faker->numberBetween(0, 1000),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => fn(array $attributes) => $attributes['created_at'],
        ];
    }

    /**
     * Indicate that the event is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the event is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->addDays(rand(1, 30)),
            'end_date' => now()->addDays(rand(2, 31)),
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the event is free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0,
            'is_free' => true,
        ]);
    }

    /**
     * Indicate that the event is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the event is at full capacity.
     */
    public function atCapacity(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_attendees' => 100,
            'current_attendees' => 100,
        ]);
    }
}
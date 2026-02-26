<?php

namespace App\Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Events\Models\EventCategory;
use App\Modules\Events\Models\Event;
use App\Models\User;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'Conference', 'color' => '#3498db', 'icon' => 'fa-calendar'],
            ['name' => 'Workshop', 'color' => '#2ecc71', 'icon' => 'fa-code'],
            ['name' => 'Seminar', 'color' => '#f39c12', 'icon' => 'fa-chalkboard'],
            ['name' => 'Meetup', 'color' => '#e74c3c', 'icon' => 'fa-users'],
            ['name' => 'Webinar', 'color' => '#9b59b6', 'icon' => 'fa-video'],
        ];

        foreach ($categories as $category) {
            EventCategory::create([
                'name' => $category['name'],
                'slug' => \Str::slug($category['name']),
                'color' => $category['color'],
                'icon' => $category['icon'],
                'description' => $category['name'] . ' events category',
            ]);
        }

        // Create sample events if in development
        if (app()->environment('local')) {
            $user = User::first() ?? User::factory()->create();
            
            Event::factory()
                ->count(20)
                ->create([
                    'user_id' => $user->id,
                    'category_id' => EventCategory::inRandomOrder()->first()->id,
                ]);
        }
    }
}
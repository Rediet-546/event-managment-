<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('event_categories');
            $table->foreignId('user_id')->constrained('users'); // creator/organizer
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('venue');
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Date and Time
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->datetime('registration_deadline')->nullable();
            
            // Capacity and Pricing
            $table->integer('max_attendees')->nullable();
            $table->integer('current_attendees')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_free')->default(false);
            
            // Status and Visibility
            $table->string('status')->default('draft'); // draft, published, cancelled, completed
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_virtual')->default(false);
            $table->string('virtual_link')->nullable();
            
            // Meta
            $table->json('meta_data')->nullable();
            $table->integer('views')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'start_date']);
            $table->index(['city', 'country']);
            $table->index('is_featured');
            $table->index('user_id');
            $table->index('category_id');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
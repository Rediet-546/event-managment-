<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('booking_reference')->unique();
            $table->unsignedInteger('tickets_count')->default(1);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_method')->nullable();

            $table->string('status')->default('confirmed'); // confirmed, cancelled
            $table->dateTime('booking_date')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->json('meta_data')->nullable();

            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};


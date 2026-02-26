<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendee_booking_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('attendee_bookings')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendee_booking_history');
    }
};

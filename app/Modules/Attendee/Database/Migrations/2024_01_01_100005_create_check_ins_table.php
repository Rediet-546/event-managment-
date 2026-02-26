<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendee_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('attendee_bookings')->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained('attendee_tickets')->onDelete('cascade');
            $table->foreignId('checked_in_by')->constrained('users');
            $table->timestamp('checked_in_at');
            $table->string('method')->default('qr');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendee_check_ins');
    }
};

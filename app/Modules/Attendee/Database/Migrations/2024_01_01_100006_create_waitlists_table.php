<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendee_waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained('attendee_ticket_types');
            $table->integer('quantity')->default(1);
            $table->integer('position');
            $table->string('status')->default('waiting');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            
            $table->unique(['event_id', 'user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendee_waitlists');
    }
};

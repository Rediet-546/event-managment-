<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendee_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // confirmation, reminder, cancellation
            $table->string('subject');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('variables')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendee_email_templates');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('type'); // image, video, document
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('size')->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['event_id', 'is_primary']);
            $table->index(['event_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_media');
    }
};
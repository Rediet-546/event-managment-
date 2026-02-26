<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add publishing fields
            $table->timestamp('publish_at')->nullable()->after('status');
            $table->timestamp('published_at')->nullable()->after('publish_at');
            $table->timestamp('cancelled_at')->nullable()->after('published_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');

            // Add index for better performance
            $table->index(['status', 'publish_at']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'publish_at',
                'published_at',
                'cancelled_at',
                'cancellation_reason'
            ]);
        });
    }
};
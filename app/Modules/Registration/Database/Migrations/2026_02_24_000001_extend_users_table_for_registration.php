<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type')->default('attendee')->after('age');
            }
            if (!Schema::hasColumn('users', 'organization_name')) {
                $table->string('organization_name')->nullable()->after('user_type');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('organization_name');
            }
            if (!Schema::hasColumn('users', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('tax_id');
            }
            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('is_approved');
            }
            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('approved_by');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        // Best-effort down. (SQLite may not support dropping columns easily.)
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'first_name',
                'last_name',
                'username',
                'age',
                'user_type',
                'organization_name',
                'phone',
                'tax_id',
                'is_approved',
                'approved_at',
                'approved_by',
                'is_active',
                'last_login_at',
                'last_login_ip',
                'deleted_at',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    try {
                        $table->dropColumn($column);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        });
    }
};


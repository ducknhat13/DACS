<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add notification preferences to users table
 * 
 * Thêm 2 columns cho notification preferences:
 * - email_on_vote: Nhận email khi poll của mình có vote mới (default: true)
 * - notify_before_autoclose: Nhận email nhắc nhở trước khi poll auto-close (default: true)
 * 
 * Default: true để user nhận notifications mặc định
 * User có thể tắt trong Profile settings
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Notification preferences với default = true
            $table->boolean('email_on_vote')->default(true)->after('email_verified_at');
            $table->boolean('notify_before_autoclose')->default(true)->after('email_on_vote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_on_vote', 'notify_before_autoclose']);
        });
    }
};

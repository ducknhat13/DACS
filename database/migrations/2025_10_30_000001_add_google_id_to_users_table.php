<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add google_id to users table
 * 
 * Thêm google_id column để hỗ trợ Google OAuth login:
 * - google_id: ID từ Google OAuth (nullable, unique)
 * - Unique constraint: Mỗi Google ID chỉ link với 1 user
 * 
 * Google OAuth flow:
 * - User login qua Google → Lưu google_id
 * - Có thể link Google account với existing account
 * - OAuth-only users không có local password
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth ID (nullable vì user có thể không dùng Google login)
            $table->string('google_id')->nullable()->unique()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_id');
        });
    }
};



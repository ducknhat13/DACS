<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add has_local_password to users table
 * 
 * Thêm has_local_password column để phân biệt:
 * - has_local_password = true: User có password local (có thể đổi password, xóa account cần password)
 * - has_local_password = false: User chỉ dùng Google OAuth (không có password local, xóa account không cần password)
 * 
 * Default: true (backward compatible cho existing users)
 * 
 * Được dùng trong:
 * - ProfileController@destroy: Có yêu cầu password không khi xóa account
 * - Password update form: Hiển thị/ẩn form đổi password
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Boolean: User có password local không (default: true cho backward compatible)
            $table->boolean('has_local_password')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_local_password');
        });
    }
};



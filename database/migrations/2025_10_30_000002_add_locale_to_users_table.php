<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add locale to users table
 * 
 * Thêm locale column để lưu ngôn ngữ preference của user:
 * - locale: Ngôn ngữ preference ('vi' hoặc 'en')
 * - Default: 'vi' (tiếng Việt)
 * 
 * Locale được dùng để:
 * - Hiển thị giao diện theo ngôn ngữ user chọn
 * - Email notifications theo ngôn ngữ user
 * - Đồng bộ với session locale
 */
return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            // Ngôn ngữ preference: 'vi' hoặc 'en' (default: 'vi')
            $table->string('locale', 5)->default('vi');
        });
    }
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};

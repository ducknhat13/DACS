<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add status and privacy columns to polls table
 * 
 * Thêm 3 columns cho poll status và privacy:
 * - is_closed: Poll đã đóng chưa (default: false)
 * - is_private: Poll có private không (cần access key, default: false)
 * - access_key: Access key cho private polls (nullable, max 64 chars)
 * 
 * Privacy flow:
 * - is_private = true: Poll chỉ accessible với access_key đúng
 * - access_key: Tự động generate nếu không được user nhập (8 ký tự uppercase)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->boolean('is_closed')->default(false)->after('allow_multiple'); // Poll đã đóng chưa
            $table->boolean('is_private')->default(false)->after('is_closed'); // Poll có private không
            $table->string('access_key', 64)->nullable()->after('is_private'); // Access key cho private polls
        });
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['is_closed', 'is_private', 'access_key']);
        });
    }
};



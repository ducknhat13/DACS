<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add voter_name to votes table
 * 
 * Thêm voter_name column để hiển thị tên người vote:
 * - voter_name: Tên người vote (từ account hoặc session)
 * - Nullable: Có thể không có nếu user không nhập tên
 * - Max 100 chars
 * 
 * Được dùng cho:
 * - Hiển thị trong results page
 * - Email notifications
 * - Export CSV
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Tên người vote (từ Auth::user()->name hoặc session['voter_name'])
            $table->string('voter_name', 100)->nullable()->after('voter_identifier');
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropColumn('voter_name');
        });
    }
};



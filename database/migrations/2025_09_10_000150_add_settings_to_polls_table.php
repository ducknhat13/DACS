<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add settings columns to polls table
 * 
 * Thêm 4 columns cho poll settings:
 * - voting_security: 'session' (public) hoặc 'private' (cần access key)
 * - auto_close_at: Thời gian tự động đóng poll (nullable)
 * - allow_comments: Cho phép bình luận (default: false)
 * - hide_share: Ẩn nút chia sẻ (default: false)
 * 
 * Note: voting_security được map thành is_private trong code
 * (để backward compatible với is_private flag)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->string('voting_security')->default('session'); // 'session' hoặc 'private'
            $table->timestamp('auto_close_at')->nullable(); // Thời gian tự động đóng poll
            $table->boolean('allow_comments')->default(false); // Cho phép bình luận
            $table->boolean('hide_share')->default(false); // Ẩn nút chia sẻ
        });
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['voting_security','auto_close_at','allow_comments','hide_share']);
        });
    }
};



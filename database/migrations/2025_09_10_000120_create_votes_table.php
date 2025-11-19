<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create votes table
 * 
 * Tạo bảng votes - lưu các bình chọn (votes) của users
 * 
 * Columns:
 * - id: Primary key
 * - poll_option_id: Foreign key đến poll_options
 * - poll_id: Foreign key đến polls (redundant nhưng giúp query nhanh)
 * - user_id: Foreign key đến users (nullable cho guest votes)
 * - ip_address: IP address của voter (IPv6 max 45 chars)
 * - session_id: Session ID của voter (nullable)
 * - voter_identifier: Unique identifier ("user_{id}" hoặc "session_{session_id}")
 * - rank: Được thêm sau cho ranking polls
 * - voter_name: Được thêm sau để hiển thị tên voter
 * - timestamps: created_at, updated_at
 * 
 * Indexes:
 * - Unique constraint: Mỗi voter chỉ vote 1 lần cho mỗi option
 * - Indexes: Để query nhanh theo poll_id, poll_option_id, voter_identifier
 * 
 * Note: rank column được thêm sau trong migration: add_rank_to_votes_table
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->foreignId('poll_option_id')->constrained('poll_options')->cascadeOnDelete();
            $table->foreignId('poll_id')->constrained('polls')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete(); // Nullable cho guest votes
            
            // Tracking columns
            $table->string('ip_address', 45); // IPv6 max length = 45
            $table->string('session_id', 100)->nullable(); // Session ID (guest votes)
            $table->string('voter_identifier', 160); // "user_{id}" hoặc "session_{session_id}"
            $table->timestamps(); // created_at, updated_at

            /**
             * Unique constraint: Mỗi voter chỉ vote 1 lần cho mỗi option
             * - Cho phép user vote nhiều options (multiple choice)
             * - Nhưng không cho phép vote lại option đã vote
             */
            $table->unique(['poll_id', 'poll_option_id', 'voter_identifier'], 'votes_unique_per_option_voter');

            /**
             * Indexes để query nhanh:
             * - poll_id: Lấy tất cả votes của một poll
             * - poll_option_id: Đếm votes cho một option
             * - voter_identifier: Check user đã vote chưa
             */
            $table->index(['poll_id']);
            $table->index(['poll_option_id']);
            $table->index(['voter_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};



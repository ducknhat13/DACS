<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create comments table
 * 
 * Tạo bảng comments - lưu các bình luận trên polls
 * 
 * Columns:
 * - id: Primary key
 * - poll_id: Foreign key đến polls (cascadeOnDelete)
 * - user_id: Được thêm sau để link với logged in users
 * - voter_name: Tên người comment (từ account hoặc session)
 * - content: Nội dung comment (text)
 * - session_id: Session ID của guest (nullable)
 * - ip_address: Được thêm sau để tracking
 * - timestamps: created_at, updated_at
 * 
 * Note: user_id và ip_address được thêm trong migrations sau
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            // Foreign key đến polls (cascade delete: xóa poll → xóa comments)
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->string('voter_name')->nullable(); // Tên người comment (từ account hoặc session)
            $table->text('content'); // Nội dung comment
            $table->string('session_id')->nullable(); // Session ID (cho guest comments)
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};



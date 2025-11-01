<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create polls table
 * 
 * Tạo bảng polls - bảng chính lưu thông tin các khảo sát
 * 
 * Columns:
 * - id: Primary key
 * - user_id: Foreign key đến users (nullable, nullOnDelete nếu user bị xóa)
 * - question: Câu hỏi của poll (text, có thể có title riêng trong migration sau)
 * - slug: Unique identifier cho poll (dùng trong URL)
 * - allow_multiple: Cho phép chọn nhiều options (boolean)
 * - timestamps: created_at, updated_at
 * 
 * Note: Các columns khác được thêm trong các migrations sau:
 * - title, description, poll_type, is_private, access_key, etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            // Foreign key đến users table (nullable vì có thể là guest poll trong tương lai)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('question'); // Câu hỏi chính của poll
            $table->string('slug')->unique(); // Unique slug cho URL (ví dụ: "meo-chay-xanh")
            $table->boolean('allow_multiple')->default(false); // Cho phép multiple choice
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};



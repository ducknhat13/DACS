<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create poll_options table
 * 
 * Tạo bảng poll_options - lưu các lựa chọn (options) của mỗi poll
 * 
 * Columns:
 * - id: Primary key
 * - poll_id: Foreign key đến polls (cascadeOnDelete: xóa poll → xóa options)
 * - option_text: Text của option (có thể có image_url trong migration sau)
 * - timestamps: created_at, updated_at
 * 
 * Note: Image support (image_url, image_title, image_alt_text) được thêm sau
 * trong migration: add_image_support_to_polls_and_options
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            // Foreign key đến polls (cascade delete: xóa poll → tự động xóa options)
            $table->foreignId('poll_id')->constrained('polls')->cascadeOnDelete();
            $table->string('option_text'); // Text của option (hoặc title cho image polls)
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_options');
    }
};



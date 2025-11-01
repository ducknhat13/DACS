<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add poll_type to polls table
 * 
 * Thêm poll_type column để phân biệt các loại poll:
 * - 'regular': Poll thông thường (single/multiple choice)
 * - 'ranking': Poll xếp hạng (user phải xếp hạng tất cả options)
 * 
 * Note: Sau này được mở rộng thêm 'image' type trong migration khác
 * Default: 'regular' để backward compatible
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            // Enum với 2 giá trị ban đầu (sau được mở rộng thêm 'image')
            $table->enum('poll_type', ['regular', 'ranking'])->default('regular')->after('allow_multiple');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn('poll_type');
        });
    }
};

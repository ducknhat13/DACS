<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add image support to polls and options
 * 
 * Mở rộng polls và poll_options để hỗ trợ image polls:
 * 
 * Polls table:
 * - Mở rộng poll_type enum: thêm 'image' (từ 'regular' → 'standard')
 * - max_image_selections: Giới hạn số lượng images được chọn (nullable, reuse cho standard multiple choice)
 * 
 * Poll_options table:
 * - image_url: URL hoặc path đến image
 * - image_alt_text: Alt text cho accessibility
 * - image_title: Title/label để hiển thị (thay cho option_text cho image polls)
 * 
 * Note: max_image_selections được reuse cho standard multiple choice polls (lưu max_choices)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * Cập nhật polls table:
         * - Drop và recreate poll_type enum với 'image' type
         * - Thêm max_image_selections (reuse cho standard multiple choice)
         */
        Schema::table('polls', function (Blueprint $table) {
            // Drop enum cũ (chỉ có 'regular', 'ranking')
            $table->dropColumn('poll_type');
        });
        
        Schema::table('polls', function (Blueprint $table) {
            // Recreate với 'standard' (thay 'regular'), 'ranking', 'image'
            $table->enum('poll_type', ['standard', 'ranking', 'image'])->default('standard')->after('allow_multiple');
            // Max selections: Dùng cho image polls và standard multiple choice (lưu max_choices)
            $table->integer('max_image_selections')->nullable()->after('poll_type')->comment('Maximum number of images that can be selected (for image polls)');
        });

        /**
         * Cập nhật poll_options table: Thêm image support
         */
        Schema::table('poll_options', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('option_text')->comment('URL or path to the image for this option');
            $table->string('image_alt_text')->nullable()->after('image_url')->comment('Alt text for the image');
            $table->string('image_title')->nullable()->after('image_alt_text')->comment('Title/label for the image option');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['max_image_selections']);
            $table->dropColumn('poll_type');
        });
        
        Schema::table('polls', function (Blueprint $table) {
            $table->enum('poll_type', ['standard', 'ranking'])->default('standard')->after('allow_multiple');
        });

        Schema::table('poll_options', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'image_alt_text', 'image_title']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add description_media to polls table
 * 
 * Thêm description_media column để hỗ trợ media files trong poll description:
 * - description_media: JSON array chứa URLs của images/videos
 * - Format: ["url1", "url2", ...]
 * - Nullable: Không bắt buộc
 * 
 * Được dùng để:
 * - Hiển thị images/videos trong poll description
 * - Rich text description với media support
 * 
 * Note: Media files được upload qua ImageUploadController hoặc validate URLs
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            // JSON array chứa URLs của media files (images/videos) cho description
            $table->json('description_media')->nullable()->after('description')->comment('JSON array of media files (images/videos) for description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn('description_media');
        });
    }
};
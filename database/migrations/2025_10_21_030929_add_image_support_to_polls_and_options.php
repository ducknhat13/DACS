<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật polls table để hỗ trợ image type
        Schema::table('polls', function (Blueprint $table) {
            // Thay đổi enum poll_type để bao gồm 'image'
            $table->dropColumn('poll_type');
        });
        
        Schema::table('polls', function (Blueprint $table) {
            $table->enum('poll_type', ['standard', 'ranking', 'image'])->default('standard')->after('allow_multiple');
            $table->integer('max_image_selections')->nullable()->after('poll_type')->comment('Maximum number of images that can be selected (for image polls)');
        });

        // Cập nhật poll_options table để hỗ trợ hình ảnh
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
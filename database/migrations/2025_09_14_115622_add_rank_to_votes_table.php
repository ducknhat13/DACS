<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add rank column to votes table
 * 
 * Thêm rank column cho ranking polls:
 * - rank: Vị trí xếp hạng của option (1 = best, n = worst)
 * - Nullable: Chỉ có giá trị cho ranking polls
 * 
 * Ranking logic:
 * - Mỗi voter phải rank TẤT CẢ options
 * - rank = 1: Option được yêu thích nhất
 * - rank = n: Option được yêu thích ít nhất (n = số lượng options)
 * - Borda Count: Tính điểm dựa trên rank
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Rank column: 1 = best, n = worst (nullable cho regular polls)
            $table->integer('rank')->nullable()->after('poll_option_id');
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }
};

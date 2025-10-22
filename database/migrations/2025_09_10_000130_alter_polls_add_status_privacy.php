<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->boolean('is_closed')->default(false)->after('allow_multiple');
            $table->boolean('is_private')->default(false)->after('is_closed');
            $table->string('access_key', 64)->nullable()->after('is_private');
        });
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['is_closed', 'is_private', 'access_key']);
        });
    }
};



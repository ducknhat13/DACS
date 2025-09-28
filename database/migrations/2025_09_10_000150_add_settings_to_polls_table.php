<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->string('voting_security')->default('session');
            $table->timestamp('auto_close_at')->nullable();
            $table->boolean('allow_comments')->default(false);
            $table->boolean('hide_share')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['voting_security','auto_close_at','allow_comments','hide_share']);
        });
    }
};



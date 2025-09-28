<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_option_id')->constrained('poll_options')->cascadeOnDelete();
            $table->foreignId('poll_id')->constrained('polls')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->string('session_id', 100)->nullable();
            $table->string('voter_identifier', 160);
            $table->timestamps();

            // Unique theo chiến lược đa chọn: mỗi option 1 lần theo voter
            $table->unique(['poll_id', 'poll_option_id', 'voter_identifier'], 'votes_unique_per_option_voter');

            // Chỉ mục hỗ trợ truy vấn nhanh
            $table->index(['poll_id']);
            $table->index(['poll_option_id']);
            $table->index(['voter_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};



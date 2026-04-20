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
        if (! Schema::hasTable('user_topic_progress')) {
            Schema::create('user_topic_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreignId('topic_id')->constrained();
                $table->unsignedInteger('attempts_count')->default(0);
                $table->unsignedInteger('correct_answers')->default(0);
                $table->unsignedInteger('total_answers')->default(0);
                $table->decimal('mastery_percent', 5, 2)->default(0);
                $table->timestamp('last_attempted_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'topic_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_topic_progress');
    }
};

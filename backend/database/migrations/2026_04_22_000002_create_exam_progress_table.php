<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('exam_progress')) {
            Schema::create('exam_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('attempts_count')->default(0);
                $table->unsignedInteger('passed_attempts')->default(0);
                $table->decimal('average_score', 5, 2)->default(0);
                $table->decimal('best_score', 5, 2)->default(0);
                $table->timestamp('last_attempted_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'exam_id']);
                $table->index(['user_id', 'attempts_count']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_progress');
    }
};

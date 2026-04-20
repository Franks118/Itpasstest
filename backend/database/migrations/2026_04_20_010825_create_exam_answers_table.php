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
        if (! Schema::hasTable('exam_answers')) {
            Schema::create('exam_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_session_id')->constrained()->cascadeOnDelete();
                $table->foreignId('question_id')->constrained();
                $table->foreignId('question_option_id')->nullable()->constrained('question_options');
                $table->boolean('is_correct')->default(false);
                $table->timestamps();

                $table->unique(['exam_session_id', 'question_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};

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
        if (! Schema::hasTable('questions')) {
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
                $table->foreignId('topic_id')->constrained();
                $table->text('question_text');
                $table->text('explanation')->nullable();
                $table->string('difficulty')->default('medium');
                $table->unsignedSmallInteger('points')->default(1);
                $table->unsignedInteger('order_index')->default(1);
                $table->timestamps();

                $table->index(['exam_id', 'order_index']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

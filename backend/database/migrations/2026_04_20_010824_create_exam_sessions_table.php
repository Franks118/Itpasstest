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
        if (! Schema::hasTable('exam_sessions')) {
            Schema::create('exam_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreignId('exam_id')->constrained();
                $table->timestamp('started_at');
                $table->timestamp('submitted_at')->nullable();
                $table->string('status')->default('in_progress');
                $table->decimal('score', 5, 2)->default(0);
                $table->unsignedInteger('correct_answers')->default(0);
                $table->unsignedInteger('total_questions')->default(0);
                $table->timestamps();

                $table->index(['user_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};

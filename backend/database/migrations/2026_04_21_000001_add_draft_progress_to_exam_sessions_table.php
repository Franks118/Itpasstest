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
            return;
        }

        Schema::table('exam_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('exam_sessions', 'current_question_index')) {
                $table->unsignedInteger('current_question_index')->default(0)->after('total_questions');
            }
            if (! Schema::hasColumn('exam_sessions', 'draft_answers')) {
                $table->json('draft_answers')->nullable()->after('current_question_index');
            }
            if (! Schema::hasColumn('exam_sessions', 'question_order')) {
                $table->json('question_order')->nullable()->after('draft_answers');
            }
            if (! Schema::hasColumn('exam_sessions', 'progress_saved_at')) {
                $table->timestamp('progress_saved_at')->nullable()->after('question_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('exam_sessions')) {
            return;
        }

        Schema::table('exam_sessions', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('exam_sessions', 'progress_saved_at')) {
                $drops[] = 'progress_saved_at';
            }
            if (Schema::hasColumn('exam_sessions', 'question_order')) {
                $drops[] = 'question_order';
            }
            if (Schema::hasColumn('exam_sessions', 'draft_answers')) {
                $drops[] = 'draft_answers';
            }
            if (Schema::hasColumn('exam_sessions', 'current_question_index')) {
                $drops[] = 'current_question_index';
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};

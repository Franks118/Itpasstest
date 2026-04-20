<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('exams', 'shuffle_questions')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->boolean('shuffle_questions')->default(false)->after('total_questions');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('exams', 'shuffle_questions')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropColumn('shuffle_questions');
            });
        }
    }
};

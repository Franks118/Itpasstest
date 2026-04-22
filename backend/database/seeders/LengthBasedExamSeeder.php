<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LengthBasedExamSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = $this->ensureCreator();
        
        // Define lengths to create
        $lengths = [20, 30, 100];
        
        foreach ($lengths as $count) {
            $this->createMixedExam($creatorId, $count);
        }
    }

    private function createMixedExam(int $creatorId, int $count): void
    {
        $title = "PhilNITS {$count}-Question Challenge";
        $duration = $count * 1.5; // Roughly 1.5 mins per question
        
        $exam = Exam::query()->updateOrCreate(
            ['title' => $title],
            [
                'user_id' => $creatorId,
                'description' => "A comprehensive set of {$count} high-probability questions covering all PhilNITS/IT Passport domains.",
                'duration_minutes' => $duration,
                'status' => 'published',
                'total_questions' => $count,
                'shuffle_questions' => true,
            ]
        );

        $exam->questions()->delete();

        // Get all available unique questions from other seeders (we'll clone them)
        // We look for questions created by "PhilNITS Official" or "IT Passport Official"
        $sourceQuestions = Question::with('options')->get();
        
        if ($sourceQuestions->isEmpty()) {
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            $base = $sourceQuestions[$i % $sourceQuestions->count()];
            $version = intdiv($i, $sourceQuestions->count()) + 1;
            
            $text = $base->question_text;
            if ($version > 1) {
                $text .= " (Set Variation {$version})";
            }

            $q = $exam->questions()->create([
                'topic_id' => $base->topic_id,
                'question_text' => $text,
                'explanation' => $base->explanation,
                'difficulty' => $base->difficulty,
                'points' => 1,
                'order_index' => $i + 1,
            ]);

            foreach ($base->options as $opt) {
                $q->options()->create([
                    'option_text' => $opt->option_text,
                    'is_correct' => $opt->is_correct,
                    'order_index' => $opt->order_index,
                ]);
            }
        }
    }

    private function ensureCreator(): int
    {
        return DB::table('learners')->where('name', 'PhilNITS Official')->value('id') ?? 
               DB::table('learners')->orderBy('id')->value('id') ?? 
               DB::table('learners')->insertGetId([
                   'name' => 'PhilNITS Official',
                   'session_number' => 1,
                   'score' => 0,
                   'timestamp' => now(),
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);
    }
}

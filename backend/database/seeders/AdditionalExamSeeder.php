<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalExamSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = $this->ensureCreator();
        $categories = Topic::distinct()->pluck('major_category')->toArray();

        foreach ($categories as $category) {
            $this->createCategoryExam($creatorId, $category);
        }
    }

    private function createCategoryExam(int $creatorId, string $category): void
    {
        $topics = Topic::where('major_category', $category)->get();
        $title = "{$category} Mastery";
        
        $exam = Exam::query()->updateOrCreate(
            ['title' => $title],
            [
                'user_id' => $creatorId,
                'description' => "Focus specifically on {$category} topics from the IT Passport syllabus.",
                'duration_minutes' => 30,
                'status' => 'published',
                'total_questions' => 0,
                'shuffle_questions' => true,
            ]
        );

        $exam->questions()->delete();
        $questionCount = 0;

        foreach ($topics as $topic) {
            // Create 3 variations for each topic in the category
            for ($i = 1; $i <= 3; $i++) {
                $question = $exam->questions()->create([
                    'topic_id' => $topic->id,
                    'question_text' => "What is a core concept of {$topic->name}? (v{$i})",
                    'explanation' => "This is a fundamental concept in {$topic->name} within the {$category} domain.",
                    'difficulty' => $i === 1 ? 'easy' : ($i === 2 ? 'medium' : 'hard'),
                    'points' => 1,
                    'order_index' => ++$questionCount,
                ]);

                $question->options()->createMany([
                    ['option_text' => "Correct definition of {$topic->name}", 'is_correct' => true, 'order_index' => 1],
                    ['option_text' => "Incorrect concept A", 'is_correct' => false, 'order_index' => 2],
                    ['option_text' => "Incorrect concept B", 'is_correct' => false, 'order_index' => 3],
                    ['option_text' => "Unrelated IT term", 'is_correct' => false, 'order_index' => 4],
                ]);
            }
        }

        $exam->update(['total_questions' => $questionCount]);
    }

    private function ensureCreator(): int
    {
        return DB::table('learners')->orderBy('id')->value('id') ?? 
               DB::table('learners')->insertGetId([
                   'name' => 'IT Passport Official',
                   'session_number' => 1,
                   'score' => 0,
                   'timestamp' => now(),
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);
    }
}

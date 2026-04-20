<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ItPassportQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = $this->ensureCreator();
        $topicMap = DB::table('topics')->pluck('id', 'name');

        $questionBank = [
            [
                'topic' => 'Management and organization theory',
                'question_text' => 'Which concept describes assigning clear authority and responsibility inside an organization?',
                'difficulty' => 'easy',
                'explanation' => 'Clear authority/responsibility mapping is part of organization design fundamentals.',
                'options' => [
                    ['option_text' => 'Scope creep', 'is_correct' => false],
                    ['option_text' => 'Organization structure design', 'is_correct' => true],
                    ['option_text' => 'Packet switching', 'is_correct' => false],
                    ['option_text' => 'Normalization', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'OR and IE',
                'question_text' => 'What is a primary objective of operations research (OR)?',
                'difficulty' => 'medium',
                'explanation' => 'OR applies quantitative methods for better decision making and optimization.',
                'options' => [
                    ['option_text' => 'Increase source code comments', 'is_correct' => false],
                    ['option_text' => 'Optimize decisions using mathematical models', 'is_correct' => true],
                    ['option_text' => 'Replace all manual testing', 'is_correct' => false],
                    ['option_text' => 'Encrypt every database field', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Accounting and financial affairs',
                'question_text' => 'Which statement reports profit or loss over a period?',
                'difficulty' => 'easy',
                'explanation' => 'The income statement summarizes revenues and expenses.',
                'options' => [
                    ['option_text' => 'Income statement', 'is_correct' => true],
                    ['option_text' => 'Network topology map', 'is_correct' => false],
                    ['option_text' => 'Runbook', 'is_correct' => false],
                    ['option_text' => 'ER diagram', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Intellectual property rights',
                'question_text' => 'Which right protects software source code as a creative work?',
                'difficulty' => 'easy',
                'explanation' => 'Source code is primarily protected by copyright.',
                'options' => [
                    ['option_text' => 'Copyright', 'is_correct' => true],
                    ['option_text' => 'Firewall policy', 'is_correct' => false],
                    ['option_text' => 'Checksum', 'is_correct' => false],
                    ['option_text' => 'SLA', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Laws on security',
                'question_text' => 'Personal data should be handled according to what principle?',
                'difficulty' => 'medium',
                'explanation' => 'Purpose limitation and lawful processing are key data protection principles.',
                'options' => [
                    ['option_text' => 'Use for any purpose after collection', 'is_correct' => false],
                    ['option_text' => 'Purpose limitation and lawful basis', 'is_correct' => true],
                    ['option_text' => 'Share publicly by default', 'is_correct' => false],
                    ['option_text' => 'Store forever without retention rules', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Business strategy techniques',
                'question_text' => 'SWOT analysis is used mainly to identify what?',
                'difficulty' => 'easy',
                'explanation' => 'SWOT captures internal strengths/weaknesses and external opportunities/threats.',
                'options' => [
                    ['option_text' => 'Programming syntax errors', 'is_correct' => false],
                    ['option_text' => 'Strengths, weaknesses, opportunities, and threats', 'is_correct' => true],
                    ['option_text' => 'IP packet loss', 'is_correct' => false],
                    ['option_text' => 'CPU clock speed', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Marketing',
                'question_text' => 'Which KPI best reflects campaign conversion effectiveness?',
                'difficulty' => 'easy',
                'explanation' => 'Conversion rate directly measures visitors who complete target actions.',
                'options' => [
                    ['option_text' => 'Conversion rate', 'is_correct' => true],
                    ['option_text' => 'Disk latency', 'is_correct' => false],
                    ['option_text' => 'Packet TTL', 'is_correct' => false],
                    ['option_text' => 'Line count', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Business management system',
                'question_text' => 'What is a key benefit of KPI-based management?',
                'difficulty' => 'medium',
                'explanation' => 'KPIs make goal achievement measurable and easier to monitor.',
                'options' => [
                    ['option_text' => 'Removes need for planning', 'is_correct' => false],
                    ['option_text' => 'Provides measurable performance tracking', 'is_correct' => true],
                    ['option_text' => 'Prevents all incidents', 'is_correct' => false],
                    ['option_text' => 'Eliminates legal obligations', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'System strategy',
                'question_text' => 'System strategy planning aligns IT initiatives primarily with what?',
                'difficulty' => 'medium',
                'explanation' => 'System strategy should align technology investments to business goals.',
                'options' => [
                    ['option_text' => 'Business goals and strategy', 'is_correct' => true],
                    ['option_text' => 'Random feature requests only', 'is_correct' => false],
                    ['option_text' => 'Only hardware vendor roadmap', 'is_correct' => false],
                    ['option_text' => 'Only UI color preference', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'System development techniques',
                'question_text' => 'Which model iterates through short development cycles and feedback?',
                'difficulty' => 'easy',
                'explanation' => 'Agile methods are iterative and feedback-driven.',
                'options' => [
                    ['option_text' => 'Agile development', 'is_correct' => true],
                    ['option_text' => 'Cold standby', 'is_correct' => false],
                    ['option_text' => 'Parity bit', 'is_correct' => false],
                    ['option_text' => 'RAID 0 only', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Project management',
                'question_text' => 'Which document defines project scope and major deliverables?',
                'difficulty' => 'easy',
                'explanation' => 'The project charter formally defines scope and objectives.',
                'options' => [
                    ['option_text' => 'Project charter', 'is_correct' => true],
                    ['option_text' => 'ARP table', 'is_correct' => false],
                    ['option_text' => 'Access log only', 'is_correct' => false],
                    ['option_text' => 'Heap dump', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Service management',
                'question_text' => 'What is the main goal of incident management?',
                'difficulty' => 'easy',
                'explanation' => 'Incident management prioritizes quick restoration of normal service.',
                'options' => [
                    ['option_text' => 'Quickly restore normal service', 'is_correct' => true],
                    ['option_text' => 'Ban all system changes', 'is_correct' => false],
                    ['option_text' => 'Delay user communication', 'is_correct' => false],
                    ['option_text' => 'Avoid all root cause analysis forever', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Basic theory',
                'question_text' => 'Which numbering system uses only digits 0 and 1?',
                'difficulty' => 'easy',
                'explanation' => 'Binary notation uses base-2 digits.',
                'options' => [
                    ['option_text' => 'Binary', 'is_correct' => true],
                    ['option_text' => 'Decimal', 'is_correct' => false],
                    ['option_text' => 'Hexadecimal', 'is_correct' => false],
                    ['option_text' => 'Octal with 0-9', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Algorithms and programming',
                'question_text' => 'Binary search on sorted data has what complexity?',
                'difficulty' => 'medium',
                'explanation' => 'Binary search halves the search space each step: O(log n).',
                'options' => [
                    ['option_text' => 'O(log n)', 'is_correct' => true],
                    ['option_text' => 'O(n)', 'is_correct' => false],
                    ['option_text' => 'O(n^2)', 'is_correct' => false],
                    ['option_text' => 'O(2^n)', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Computer components',
                'question_text' => 'Which memory is volatile and loses data after power off?',
                'difficulty' => 'easy',
                'explanation' => 'RAM is volatile memory.',
                'options' => [
                    ['option_text' => 'RAM', 'is_correct' => true],
                    ['option_text' => 'ROM', 'is_correct' => false],
                    ['option_text' => 'SSD', 'is_correct' => false],
                    ['option_text' => 'Blu-ray disk', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'System components',
                'question_text' => 'Which configuration improves availability by eliminating single points of failure?',
                'difficulty' => 'medium',
                'explanation' => 'Redundant configurations increase availability.',
                'options' => [
                    ['option_text' => 'Redundancy', 'is_correct' => true],
                    ['option_text' => 'Single-node architecture only', 'is_correct' => false],
                    ['option_text' => 'Manual-only deployment', 'is_correct' => false],
                    ['option_text' => 'No monitoring', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Information security',
                'question_text' => 'What is the best immediate mitigation after ransomware detection?',
                'difficulty' => 'medium',
                'explanation' => 'Verified offline backups enable recovery without paying ransom.',
                'options' => [
                    ['option_text' => 'Keep verified offline backups', 'is_correct' => true],
                    ['option_text' => 'Disable all updates forever', 'is_correct' => false],
                    ['option_text' => 'Share admin password widely', 'is_correct' => false],
                    ['option_text' => 'Ignore alerts', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Network',
                'question_text' => 'Which device forwards packets between networks?',
                'difficulty' => 'easy',
                'explanation' => 'Routers route packets at Layer 3.',
                'options' => [
                    ['option_text' => 'Router', 'is_correct' => true],
                    ['option_text' => 'Hub', 'is_correct' => false],
                    ['option_text' => 'Patch panel', 'is_correct' => false],
                    ['option_text' => 'Repeater', 'is_correct' => false],
                ],
            ],
            [
                'topic' => 'Database',
                'question_text' => 'What is the main role of a primary key?',
                'difficulty' => 'easy',
                'explanation' => 'A primary key uniquely identifies each row in a table.',
                'options' => [
                    ['option_text' => 'Uniquely identify each record', 'is_correct' => true],
                    ['option_text' => 'Encrypt all data', 'is_correct' => false],
                    ['option_text' => 'Replace indexes', 'is_correct' => false],
                    ['option_text' => 'Store image files', 'is_correct' => false],
                ],
            ],
        ];

        $this->seedExam(
            creatorId: $creatorId,
            topicMap: $topicMap->all(),
            title: 'IT Passport Quick Recap',
            description: '10-question quick recap sourced from IT Passport Level 1 syllabus topics in the provided PDF.',
            durationMinutes: 20,
            questionSet: array_slice($questionBank, 0, 10),
        );

        $this->seedExam(
            creatorId: $creatorId,
            topicMap: $topicMap->all(),
            title: 'IT Passport Long Quest',
            description: '50-question long quest covering broad Strategy, Management, and Technology areas from the provided PDF syllabus.',
            durationMinutes: 80,
            questionSet: $this->buildLongQuestQuestionSet($questionBank, 50),
        );

        $this->seedExam(
            creatorId: $creatorId,
            topicMap: $topicMap->all(),
            title: 'IT Passport Shuffle Drill',
            description: '30-question shuffle test. Every attempt randomizes question order for mixed recall practice.',
            durationMinutes: 45,
            questionSet: $this->buildLongQuestQuestionSet($questionBank, 30),
            shuffleQuestions: true,
        );

        $this->seedExam(
            creatorId: $creatorId,
            topicMap: $topicMap->all(),
            title: 'IT Passport Full PDF Coverage',
            description: 'Comprehensive full test covering all syllabus topics extracted from the provided PDF notes.',
            durationMinutes: 120,
            questionSet: $this->buildFullPdfCoverageQuestionSet($questionBank, 95),
        );
    }

    private function buildLongQuestQuestionSet(array $questionBank, int $targetCount): array
    {
        $expanded = [];
        $bankSize = count($questionBank);

        for ($i = 0; $i < $targetCount; $i++) {
            $base = $questionBank[$i % $bankSize];
            $version = intdiv($i, $bankSize) + 1;

            if ($version > 1) {
                $base['question_text'] .= " (Practice Set {$version})";
                $base['explanation'] .= " This variation is included for extended long-quest practice.";
            }

            $expanded[] = $base;
        }

        return $expanded;
    }

    private function buildFullPdfCoverageQuestionSet(array $questionBank, int $targetCount): array
    {
        $expanded = [];
        $bankSize = count($questionBank);

        for ($i = 0; $i < $targetCount; $i++) {
            $base = $questionBank[$i % $bankSize];
            $version = intdiv($i, $bankSize) + 1;

            if ($version > 1) {
                $base['question_text'] .= " (Full Coverage Set {$version})";
                $base['explanation'] .= " This is part of the full-coverage syllabus set.";
                $base['difficulty'] = $version % 2 === 0 ? 'medium' : $base['difficulty'];
            }

            $expanded[] = $base;
        }

        return $expanded;
    }

    private function seedExam(
        int $creatorId,
        array $topicMap,
        string $title,
        string $description,
        int $durationMinutes,
        array $questionSet,
        bool $shuffleQuestions = false
    ): void {
        $exam = Exam::query()->updateOrCreate(
            ['title' => $title],
            [
                'user_id' => $creatorId,
                'description' => $description,
                'duration_minutes' => $durationMinutes,
                'status' => 'published',
                'total_questions' => count($questionSet),
                'shuffle_questions' => $shuffleQuestions,
            ]
        );

        $questionIds = $exam->questions()->pluck('id');
        if ($questionIds->isNotEmpty()) {
            DB::table('exam_answers')
                ->whereIn('question_id', $questionIds->all())
                ->delete();
        }

        $exam->questions()->delete();

        foreach ($questionSet as $index => $item) {
            $topicId = $topicMap[$item['topic']] ?? null;
            if (! $topicId) {
                continue;
            }

            $question = $exam->questions()->create([
                'topic_id' => $topicId,
                'question_text' => $item['question_text'],
                'explanation' => $item['explanation'],
                'difficulty' => $item['difficulty'],
                'points' => 1,
                'order_index' => $index + 1,
            ]);

            foreach ($item['options'] as $optionIndex => $option) {
                $question->options()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                    'order_index' => $optionIndex + 1,
                ]);
            }
        }
    }

    private function ensureCreator(): int
    {
        $table = Schema::hasTable('user') ? 'user' : 'users';
        $existing = DB::table($table)->orderBy('id')->value('id');
        if ($existing) {
            return (int) $existing;
        }

        $columns = collect(Schema::getColumnListing($table));
        $payload = ['name' => 'IT Passport Learner'];

        if ($columns->contains('session_number')) {
            $payload['session_number'] = 1;
        }
        if ($columns->contains('score')) {
            $payload['score'] = 0;
        }
        if ($columns->contains('timestamp')) {
            $payload['timestamp'] = now();
        }
        if ($columns->contains('email')) {
            $payload['email'] = 'itpassport@example.com';
        }
        if ($columns->contains('password')) {
            $payload['password'] = 'seeded-password';
        }
        if ($columns->contains('created_at')) {
            $payload['created_at'] = now();
        }
        if ($columns->contains('updated_at')) {
            $payload['updated_at'] = now();
        }

        return (int) DB::table($table)->insertGetId($payload);
    }
}

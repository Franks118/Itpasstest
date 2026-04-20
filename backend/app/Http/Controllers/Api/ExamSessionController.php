<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Learner;
use App\Models\UserTopicProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExamSessionController extends Controller
{
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
        ]);

        if (! Learner::query()->whereKey($validated['user_id'])->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'The selected user does not exist in table user.',
            ]);
        }

        $exam = Exam::query()
            ->with([
                'questions' => fn ($query) => $query->orderBy('order_index'),
                'questions.topic:id,name,major_category,middle_category',
                'questions.options' => fn ($query) => $query->orderBy('order_index'),
            ])
            ->findOrFail($validated['exam_id']);

        $questions = $exam->questions;
        if ($exam->shuffle_questions) {
            $questions = $questions->shuffle()->values();
        }

        $session = ExamSession::query()->create([
            'user_id' => $validated['user_id'],
            'exam_id' => $exam->id,
            'started_at' => now(),
            'status' => 'in_progress',
            'total_questions' => $questions->count(),
        ]);

        $examPayload = [
            'id' => $exam->id,
            'title' => $exam->title,
            'description' => $exam->description,
            'duration_minutes' => $exam->duration_minutes,
            'questions' => $questions->map(fn ($question, $index) => [
                'id' => $question->id,
                'topic_id' => $question->topic_id,
                'topic_name' => $question->topic?->name,
                'question_text' => $question->question_text,
                'difficulty' => $question->difficulty,
                'order_index' => $index + 1,
                'options' => $question->options->map(fn ($option) => [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'order_index' => $option->order_index,
                ]),
            ]),
        ];

        return response()->json([
            'session_id' => $session->id,
            'exam' => $examPayload,
        ], 201);
    }

    public function submit(Request $request, ExamSession $session): JsonResponse
    {
        if ($session->status === 'submitted') {
            return response()->json([
                'message' => 'This session was already submitted.',
                'session' => $session->load('exam:id,title'),
            ]);
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.question_id' => ['required', 'integer'],
            'answers.*.selected_option_id' => ['nullable', 'integer'],
        ]);

        $exam = $session->exam()->with([
            'questions.options',
            'questions.topic:id,name',
        ])->firstOrFail();

        $answersByQuestion = collect($validated['answers'])->keyBy('question_id');
        $correctAnswers = 0;
        $persistedAnswers = [];
        $topicStats = [];

        foreach ($exam->questions as $question) {
            $submitted = $answersByQuestion->get($question->id);
            $selectedOptionId = $submitted['selected_option_id'] ?? null;
            $selectedOption = $question->options->firstWhere('id', $selectedOptionId);
            $isCorrect = (bool) ($selectedOption?->is_correct);

            if ($isCorrect) {
                $correctAnswers++;
            }

            $persistedAnswers[] = [
                'exam_session_id' => $session->id,
                'question_id' => $question->id,
                'question_option_id' => $selectedOption?->id,
                'is_correct' => $isCorrect,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $topicStats[$question->topic_id] ??= ['attempts' => 0, 'correct' => 0];
            $topicStats[$question->topic_id]['attempts']++;
            if ($isCorrect) {
                $topicStats[$question->topic_id]['correct']++;
            }
        }

        $totalQuestions = $exam->questions->count();
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        DB::transaction(function () use ($session, $persistedAnswers, $topicStats, $score, $correctAnswers, $totalQuestions) {
            $session->answers()->delete();
            $session->answers()->insert($persistedAnswers);

            $session->update([
                'submitted_at' => now(),
                'status' => 'submitted',
                'score' => $score,
                'correct_answers' => $correctAnswers,
                'total_questions' => $totalQuestions,
            ]);

            foreach ($topicStats as $topicId => $stat) {
                $progress = UserTopicProgress::query()->firstOrCreate([
                    'user_id' => $session->user_id,
                    'topic_id' => $topicId,
                ]);

                $progress->attempts_count += $stat['attempts'];
                $progress->correct_answers += $stat['correct'];
                $progress->total_answers += $stat['attempts'];
                $progress->mastery_percent = $progress->total_answers > 0
                    ? round(($progress->correct_answers / $progress->total_answers) * 100, 2)
                    : 0;
                $progress->last_attempted_at = now();
                $progress->save();
            }
        });

        return response()->json([
            'session_id' => $session->id,
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
        ]);
    }
}

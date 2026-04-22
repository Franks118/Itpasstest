<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamProgress;
use App\Models\ExamSession;
use App\Models\Learner;
use App\Models\UserTopicProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExamSessionController extends Controller
{
    private const PASS_SCORE = 80.0;

    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
        ]);

        if (! Learner::query()->whereKey($validated['user_id'])->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'The selected learner does not exist.',
            ]);
        }

        $exam = Exam::query()
            ->with([
                'questions' => fn ($query) => $query->orderBy('order_index'),
                'questions.topic:id,name,major_category,middle_category',
                'questions.options' => fn ($query) => $query->orderBy('order_index'),
            ])
            ->findOrFail($validated['exam_id']);

        $session = ExamSession::query()
            ->where('user_id', $validated['user_id'])
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        $resumed = $session !== null;
        if (! $session) {
            $questions = $exam->shuffle_questions ? $exam->questions->shuffle()->values() : $exam->questions->values();

            $session = ExamSession::query()->create([
                'user_id' => $validated['user_id'],
                'exam_id' => $exam->id,
                'started_at' => now(),
                'status' => 'in_progress',
                'total_questions' => $questions->count(),
                'current_question_index' => 0,
                'draft_answers' => [],
                'question_order' => $questions->pluck('id')->all(),
            ]);
        }

        $questions = $this->sortQuestionsByOrder($exam->questions, $session->question_order);

        return response()->json(
            $this->buildSessionPayload($session, $exam, $questions, $resumed),
            $resumed ? 200 : 201
        );
    }

    public function saveProgress(Request $request, ExamSession $session): JsonResponse
    {
        if ($session->status !== 'in_progress') {
            return response()->json([
                'message' => 'Cannot save progress for a submitted session.',
            ], 409);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'current_question_index' => ['required', 'integer', 'min:0'],
            'answers' => ['required', 'array'],
            'answers.*.question_id' => ['required', 'integer'],
            'answers.*.selected_option_id' => ['nullable', 'integer'],
        ]);

        if ((int) $validated['user_id'] !== (int) $session->user_id) {
            throw ValidationException::withMessages([
                'user_id' => 'The provided user_id does not match this session.',
            ]);
        }

        $exam = $session->exam()->with([
            'questions.options:id,question_id',
            'questions:id,exam_id',
        ])->firstOrFail();

        $validQuestionIds = $exam->questions->pluck('id')->map(fn ($id) => (int) $id)->all();
        $validQuestionLookup = array_fill_keys($validQuestionIds, true);
        $validOptionsByQuestion = $exam->questions
            ->mapWithKeys(fn ($question) => [
                (int) $question->id => array_fill_keys(
                    $question->options->pluck('id')->map(fn ($id) => (int) $id)->all(),
                    true
                ),
            ])
            ->all();

        $normalizedAnswers = [];
        foreach ($validated['answers'] as $answer) {
            $questionId = (int) $answer['question_id'];
            if (! isset($validQuestionLookup[$questionId])) {
                throw ValidationException::withMessages([
                    'answers' => 'One or more answers reference questions outside this exam.',
                ]);
            }

            $selectedOptionId = $answer['selected_option_id'] !== null ? (int) $answer['selected_option_id'] : null;
            if ($selectedOptionId !== null && ! isset($validOptionsByQuestion[$questionId][$selectedOptionId])) {
                throw ValidationException::withMessages([
                    'answers' => 'One or more selected options are invalid for their question.',
                ]);
            }

            $normalizedAnswers[$questionId] = [
                'question_id' => $questionId,
                'selected_option_id' => $selectedOptionId,
            ];
        }

        $maxIndex = max(0, count($validQuestionIds) - 1);
        $currentQuestionIndex = min((int) $validated['current_question_index'], $maxIndex);

        $session->update([
            'current_question_index' => $currentQuestionIndex,
            'draft_answers' => array_values($normalizedAnswers),
            'progress_saved_at' => now(),
        ]);

        return response()->json([
            'session_id' => $session->id,
            'progress' => [
                'current_question_index' => $session->current_question_index,
                'answers' => $session->draft_answers ?? [],
                'saved_at' => $session->progress_saved_at,
            ],
        ]);
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
                'current_question_index' => 0,
                'draft_answers' => [],
                'progress_saved_at' => now(),
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

            $examProgress = ExamProgress::query()->firstOrCreate([
                'user_id' => $session->user_id,
                'exam_id' => $session->exam_id,
            ]);

            $previousAttempts = (int) $examProgress->attempts_count;
            $newAttempts = $previousAttempts + 1;
            $newAverage = $newAttempts > 0
                ? round((((float) $examProgress->average_score * $previousAttempts) + (float) $score) / $newAttempts, 2)
                : (float) $score;

            $examProgress->attempts_count = $newAttempts;
            $examProgress->passed_attempts += ((float) $score >= self::PASS_SCORE) ? 1 : 0;
            $examProgress->average_score = $newAverage;
            $examProgress->best_score = max((float) $examProgress->best_score, (float) $score);
            $examProgress->last_attempted_at = now();
            $examProgress->save();
        });

        return response()->json([
            'session_id' => $session->id,
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
        ]);
    }

    private function buildSessionPayload(ExamSession $session, Exam $exam, Collection $questions, bool $resumed): array
    {
        return [
            'session_id' => $session->id,
            'resumed' => $resumed,
            'progress' => [
                'current_question_index' => (int) ($session->current_question_index ?? 0),
                'answers' => $session->draft_answers ?? [],
                'saved_at' => $session->progress_saved_at,
            ],
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes,
                'questions' => $questions->values()->map(fn ($question, $index) => [
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
            ],
        ];
    }

    public function indexInProgress(int $userId): JsonResponse
    {
        $sessions = ExamSession::query()
            ->with('exam:id,title,description,total_questions')
            ->where('user_id', $userId)
            ->where('status', 'in_progress')
            ->latest('updated_at')
            ->get();

        return response()->json($sessions);
    }

    private function sortQuestionsByOrder(Collection $questions, ?array $questionOrder): Collection
    {
        if (! is_array($questionOrder) || $questionOrder === []) {
            return $questions->values();
        }

        $questionMap = $questions->keyBy('id');
        $orderedQuestions = collect($questionOrder)
            ->map(fn ($id) => $questionMap->get((int) $id))
            ->filter();

        $orderedIds = $orderedQuestions->pluck('id')->map(fn ($id) => (int) $id)->all();
        $missingQuestions = $questions->filter(fn ($question) => ! in_array((int) $question->id, $orderedIds, true));

        return $orderedQuestions->concat($missingQuestions)->values();
    }
}

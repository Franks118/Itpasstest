<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamProgress;
use App\Models\ExamSession;
use App\Models\Learner;
use App\Models\UserTopicProgress;
use Illuminate\Http\JsonResponse;

class ProgressController extends Controller
{
    private const PASS_SCORE = 80.0;

    public function show(int $userId): JsonResponse
    {
        $learner = Learner::query()->find($userId);

        if (! $learner) {
            return response()->json([
                'message' => 'No learner progress found for this ID. Make sure you have the right ID or submit an exam first.',
            ], 404);
        }

        $resolvedUserId = (int) $learner->id;

        $sessions = ExamSession::query()
            ->with('exam:id,title')
            ->where('user_id', $resolvedUserId)
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->get();

        $topicProgress = UserTopicProgress::query()
            ->with('topic:id,name,major_category,middle_category')
            ->where('user_id', $resolvedUserId)
            ->orderByDesc('mastery_percent')
            ->get();

        $examAttempts = ExamProgress::query()
            ->with('exam:id,title')
            ->where('user_id', $resolvedUserId)
            ->orderByDesc('attempts_count')
            ->get();

        if ($examAttempts->isEmpty()) {
            $examAttempts = $sessions
                ->groupBy('exam_id')
                ->map(function ($examSessions) {
                    $attempts = $examSessions->count();
                    $passedAttempts = $examSessions->filter(
                        fn ($session) => (float) $session->score >= self::PASS_SCORE
                    )->count();

                    return [
                        'exam_id' => (int) $examSessions->first()->exam_id,
                        'exam_title' => $examSessions->first()->exam?->title,
                        'attempts' => $attempts,
                        'passed_attempts' => $passedAttempts,
                        'pass_rate' => $attempts > 0 ? round(($passedAttempts / $attempts) * 100, 2) : 0.0,
                    ];
                })
                ->sortByDesc('attempts')
                ->values();
        } else {
            $examAttempts = $examAttempts->map(fn ($row) => [
                'exam_id' => (int) $row->exam_id,
                'exam_title' => $row->exam?->title,
                'attempts' => (int) $row->attempts_count,
                'passed_attempts' => (int) $row->passed_attempts,
                'pass_rate' => (int) $row->attempts_count > 0
                    ? round(((int) $row->passed_attempts / (int) $row->attempts_count) * 100, 2)
                    : 0.0,
            ]);
        }

        return response()->json([
            'pass_score' => self::PASS_SCORE,
            'user' => [
                'id' => $learner->id,
                'name' => $learner->name,
                'session_number' => $learner->session_number,
            ],
            'stats' => [
                'total_sessions' => $sessions->count(),
                'average_score' => round((float) $sessions->avg('score'), 2),
                'best_score' => round((float) $sessions->max('score'), 2),
                'total_questions_answered' => (int) $sessions->sum('total_questions'),
            ],
            'recent_sessions' => $sessions->take(10)->map(fn ($session) => [
                'id' => $session->id,
                'exam_title' => $session->exam?->title,
                'score' => (float) $session->score,
                'correct_answers' => $session->correct_answers,
                'total_questions' => $session->total_questions,
                'submitted_at' => $session->submitted_at,
            ]),
            'topic_progress' => $topicProgress->map(fn ($item) => [
                'topic_id' => $item->topic_id,
                'topic_name' => $item->topic?->name,
                'major_category' => $item->topic?->major_category,
                'middle_category' => $item->topic?->middle_category,
                'attempts_count' => $item->attempts_count,
                'correct_answers' => $item->correct_answers,
                'total_answers' => $item->total_answers,
                'mastery_percent' => (float) $item->mastery_percent,
                'last_attempted_at' => $item->last_attempted_at,
            ]),
            'exam_attempts' => $examAttempts,
        ]);
    }
}

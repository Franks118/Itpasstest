<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\Learner;
use App\Models\UserTopicProgress;
use Illuminate\Http\JsonResponse;

class ProgressController extends Controller
{
    public function show(int $userId): JsonResponse
    {
        $learner = Learner::query()->findOrFail($userId);

        $sessions = ExamSession::query()
            ->with('exam:id,title')
            ->where('user_id', $userId)
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->get();

        $topicProgress = UserTopicProgress::query()
            ->with('topic:id,name,major_category,middle_category')
            ->where('user_id', $userId)
            ->orderByDesc('mastery_percent')
            ->get();

        return response()->json([
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
        ]);
    }
}

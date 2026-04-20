<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Learner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExamController extends Controller
{
    public function index(): JsonResponse
    {
        $exams = Exam::query()
            ->withCount('questions')
            ->with('creator:id,name')
            ->latest()
            ->get();

        return response()->json($exams);
    }

    public function show(Exam $exam): JsonResponse
    {
        $exam->load([
            'creator:id,name',
            'questions' => fn ($query) => $query->orderBy('order_index'),
            'questions.topic:id,name,major_category,middle_category',
            'questions.options' => fn ($query) => $query->orderBy('order_index'),
        ]);

        return response()->json($exam);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:300'],
            'status' => ['nullable', 'in:draft,published'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.topic_id' => ['required', 'integer', 'exists:topics,id'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.difficulty' => ['nullable', 'in:easy,medium,hard'],
            'questions.*.explanation' => ['nullable', 'string'],
            'questions.*.points' => ['nullable', 'integer', 'min:1', 'max:10'],
            'questions.*.options' => ['required', 'array', 'min:2', 'max:5'],
            'questions.*.options.*.option_text' => ['required', 'string'],
            'questions.*.options.*.is_correct' => ['required', 'boolean'],
        ]);

        if (! Learner::query()->whereKey($validated['user_id'])->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'The selected user does not exist in table user.',
            ]);
        }

        foreach ($validated['questions'] as $index => $question) {
            $correctCount = collect($question['options'])->where('is_correct', true)->count();
            if ($correctCount !== 1) {
                throw ValidationException::withMessages([
                    "questions.{$index}.options" => 'Each question must have exactly one correct option.',
                ]);
            }
        }

        $exam = DB::transaction(function () use ($validated) {
            $exam = Exam::query()->create([
                'user_id' => $validated['user_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'duration_minutes' => $validated['duration_minutes'],
                'status' => $validated['status'] ?? 'published',
                'total_questions' => count($validated['questions']),
            ]);

            foreach ($validated['questions'] as $questionIndex => $questionData) {
                $question = $exam->questions()->create([
                    'topic_id' => $questionData['topic_id'],
                    'question_text' => $questionData['question_text'],
                    'difficulty' => $questionData['difficulty'] ?? 'medium',
                    'explanation' => $questionData['explanation'] ?? null,
                    'points' => $questionData['points'] ?? 1,
                    'order_index' => $questionIndex + 1,
                ]);

                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                        'order_index' => $optionIndex + 1,
                    ]);
                }
            }

            return $exam;
        });

        $exam->load([
            'questions.options',
            'questions.topic:id,name,major_category,middle_category',
        ]);

        return response()->json($exam, 201);
    }
}

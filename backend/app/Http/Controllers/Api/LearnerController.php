<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LearnerController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $learner = Learner::query()->create([
            'name' => $request->input('name', 'Learner ' . rand(1000, 9999)),
            'session_number' => 1,
            'score' => 0,
            'timestamp' => now(),
        ]);

        return response()->json($learner, 201);
    }

    public function show(int $id): JsonResponse
    {
        $learner = Learner::query()->find($id);

        if (!$learner) {
            return response()->json([
                'message' => 'Invalid Learner ID.',
            ], 404);
        }

        return response()->json($learner);
    }
}

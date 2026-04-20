<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;

class TopicController extends Controller
{
    public function index(): JsonResponse
    {
        $topics = Topic::query()
            ->orderBy('major_category')
            ->orderBy('middle_category')
            ->orderBy('name')
            ->get();

        return response()->json($topics);
    }
}

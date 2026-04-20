<?php

use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamSessionController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\TopicController;
use Illuminate\Support\Facades\Route;

Route::get('/topics', [TopicController::class, 'index']);
Route::get('/exams', [ExamController::class, 'index']);
Route::get('/exams/{exam}', [ExamController::class, 'show']);

Route::post('/sessions/start', [ExamSessionController::class, 'start']);
Route::post('/sessions/{session}/submit', [ExamSessionController::class, 'submit']);

Route::get('/users/{userId}/progress', [ProgressController::class, 'show']);

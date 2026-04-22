<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'submitted_at',
        'status',
        'score',
        'correct_answers',
        'total_questions',
        'current_question_index',
        'draft_answers',
        'question_order',
        'progress_saved_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'decimal:2',
        'current_question_index' => 'integer',
        'draft_answers' => 'array',
        'question_order' => 'array',
        'progress_saved_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class, 'user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamProgress extends Model
{
    protected $table = 'exam_progress';

    protected $fillable = [
        'user_id',
        'exam_id',
        'attempts_count',
        'passed_attempts',
        'average_score',
        'best_score',
        'last_attempted_at',
    ];

    protected $casts = [
        'average_score' => 'decimal:2',
        'best_score' => 'decimal:2',
        'last_attempted_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}

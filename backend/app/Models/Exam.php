<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'duration_minutes',
        'total_questions',
        'shuffle_questions',
        'status',
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Learner::class, 'user_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}

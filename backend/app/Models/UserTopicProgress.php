<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTopicProgress extends Model
{
    protected $table = 'user_topic_progress';

    protected $fillable = [
        'user_id',
        'topic_id',
        'attempts_count',
        'correct_answers',
        'total_answers',
        'mastery_percent',
        'last_attempted_at',
    ];

    protected $casts = [
        'last_attempted_at' => 'datetime',
        'mastery_percent' => 'decimal:2',
    ];

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class, 'user_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}

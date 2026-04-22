<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Learner extends Model
{
    protected $table = 'learners';

    protected $fillable = [
        'name',
        'session_number',
        'score',
        'timestamp',
    ];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'user_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class, 'user_id');
    }
}

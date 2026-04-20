<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $fillable = [
        'name',
        'major_category',
        'middle_category',
        'syllabus_code',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserTopicProgress::class);
    }
}

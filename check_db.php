<?php
require 'backend/vendor/autoload.php';
$app = require_once 'backend/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$exams = \App\Models\Exam::where('title', 'like', '%PhilNITS%')->orWhere('title', 'like', '%Mastery%')->get();
foreach ($exams as $e) {
    echo "Exam: {$e->title} | Questions: {$e->total_questions}\n";
}

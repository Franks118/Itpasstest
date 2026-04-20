<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class ItPassportSyllabusSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            ['name' => 'Management and organization theory', 'major_category' => 'Corporate and legal affairs', 'middle_category' => 'Corporate activities', 'syllabus_code' => '1'],
            ['name' => 'OR and IE', 'major_category' => 'Corporate and legal affairs', 'middle_category' => 'Corporate activities', 'syllabus_code' => '2'],
            ['name' => 'Accounting and financial affairs', 'major_category' => 'Corporate and legal affairs', 'middle_category' => 'Corporate activities', 'syllabus_code' => '3'],
            ['name' => 'Intellectual property rights', 'major_category' => 'Corporate and legal affairs', 'middle_category' => 'Legal affairs', 'syllabus_code' => '4'],
            ['name' => 'Laws on security', 'major_category' => 'Corporate and legal affairs', 'middle_category' => 'Legal affairs', 'syllabus_code' => '5'],
            ['name' => 'Business strategy techniques', 'major_category' => 'Business strategy', 'middle_category' => 'Business strategy management', 'syllabus_code' => '9'],
            ['name' => 'Marketing', 'major_category' => 'Business strategy', 'middle_category' => 'Business strategy management', 'syllabus_code' => '10'],
            ['name' => 'Business management system', 'major_category' => 'Business strategy', 'middle_category' => 'Business strategy management', 'syllabus_code' => '12'],
            ['name' => 'System strategy', 'major_category' => 'System strategy', 'middle_category' => 'System planning', 'syllabus_code' => '18'],
            ['name' => 'System development techniques', 'major_category' => 'Development technology', 'middle_category' => 'System development technology', 'syllabus_code' => '23'],
            ['name' => 'Project management', 'major_category' => 'Project management', 'middle_category' => 'Project management', 'syllabus_code' => '26'],
            ['name' => 'Service management', 'major_category' => 'Service management', 'middle_category' => 'Service management', 'syllabus_code' => '30'],
            ['name' => 'Basic theory', 'major_category' => 'Basic theory', 'middle_category' => 'Basic theory', 'syllabus_code' => '33'],
            ['name' => 'Algorithms and programming', 'major_category' => 'Basic theory', 'middle_category' => 'Algorithms and programming', 'syllabus_code' => '37'],
            ['name' => 'Computer components', 'major_category' => 'Computer system', 'middle_category' => 'Computer components', 'syllabus_code' => '40'],
            ['name' => 'System components', 'major_category' => 'Computer system', 'middle_category' => 'System components', 'syllabus_code' => '43'],
            ['name' => 'Information security', 'major_category' => 'Technical elements', 'middle_category' => 'Security', 'syllabus_code' => '47'],
            ['name' => 'Network', 'major_category' => 'Technical elements', 'middle_category' => 'Network', 'syllabus_code' => '51'],
            ['name' => 'Database', 'major_category' => 'Technical elements', 'middle_category' => 'Database', 'syllabus_code' => '54'],
        ];

        $timestamp = now();
        $payload = array_map(function (array $topic) use ($timestamp): array {
            return [
                ...$topic,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, $topics);

        Topic::query()->upsert(
            $payload,
            ['name', 'major_category', 'middle_category'],
            ['syllabus_code', 'updated_at']
        );
    }
}

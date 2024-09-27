<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            'Junior Graphic Designer',
            'Senior Graphic Designer',
            'Graphic Design Lead',

            'IT Sales Associate',
            'Sales Manager',

            'UI Designer',
            'UX Designer',
            'UI/UX Lead',

            'Junior Full Stack Developer',
            'Senior Full Stack Developer',
            'Full Stack Team Lead',

            'QA Tester',
            'Automation Tester',
            'QA Lead',

            'SEO Specialist',
            'PPC Specialist',

            'System Administrator',
            'Technical Support Specialist',
            '.NET Developer',
            'iOS Developer',
            'Android Developer',
            'SEO Executive',
            'Laravel Developer',
            'Content Writer',
        ];

        foreach ($positions as $position) {
            DB::table('positions')->insert(['title' => $position]);
        }
    }
}

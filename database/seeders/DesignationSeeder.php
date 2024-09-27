<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            'Graphic Designer',
            'IT Sales Executive',
            'UI/UX Designer',
            'SEO Executive (Fresher)',
            'Full Stack Developer',
            'Senior Digital Marketing Executive',
            'Digital Marketing Executive',
            'Software Tester',
            'Server Administrator',
            'Technical Support Executive',
            'IT Technical Analyst',
            '.NET Developer',
            'iOS Developer',
            'Android Developer',
            'SEO Executive',
            'Laravel Developer',
            'Content Writer',
        ];

        foreach ($designations as $designation) {
            DB::table('designations')->insert(['title' => $designation]);
        }
    }
}

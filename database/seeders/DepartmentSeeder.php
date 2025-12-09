<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'Central Graduate School', 'code' => 'CGS'],
            ['name' => 'College of Business, Accountancy and Public Administration', 'code' => 'CBAPA'],
            ['name' => 'College of Engineering', 'code' => 'COE'],
            ['name' => 'College of Agriculture', 'code' => 'CA'],
            ['name' => 'College of Arts and Sciences', 'code' => 'CAS'],
            ['name' => 'College of Education', 'code' => 'CED'],
            ['name' => 'College of Computing Studies, Information and Communication Technology', 'code' => 'CCSICT'],
            ['name' => 'College of Criminal Justice Education', 'code' => 'CCJE'],
            ['name' => 'College of Nursing', 'code' => 'CON'],
            ['name' => 'School of Veterinary Medicine', 'code' => 'SVM'],
            ['name' => 'Institute of Fisheries', 'code' => 'IF'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate([
                'name' => $dept['name'],
                'code' => $dept['code'],
            ]);
        }
    }
}

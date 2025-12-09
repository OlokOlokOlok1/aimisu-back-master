<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run()
    {
        $orgs = [
            // Campus-Wide
            'campus' => [
                ['name' => 'Supreme Student Council', 'code' => 'SSC'],
                ['name' => 'The Forum Publication', 'code' => 'TFP'],
            ],

            // College of Agriculture
            'CA' => [
                ['name' => 'Isabela Young Farmers Association', 'code' => 'IYFA'],
                ['name' => 'Philippine Junior Agricultural Executive Society', 'code' => 'PJAES'],
                ['name' => 'Student Body Organization-College of Agriculture', 'code' => 'SBO-CA'],
            ],

            // College of Arts and Sciences
            'CAS' => [
                ['name' => 'English Club', 'code' => 'EC'],
                ['name' => 'ISU Alliance of Chemistry Students', 'code' => 'IACS'],
                ['name' => 'ISU Biological Society', 'code' => 'IBS'],
                ['name' => 'ISU Mathematical Society', 'code' => 'IMS'],
                ['name' => 'Mass Communication Society', 'code' => 'MCS'],
                ['name' => 'The Psychological Society', 'code' => 'TPS'],
                ['name' => 'Student Body Organization-College of Arts and Sciences', 'code' => 'SBO-CAS'],
            ],

            // College of Education
            'CED' => [
                ['name' => 'Association of Early Childhood Educators', 'code' => 'AECE'],
                ['name' => 'Bachelor of Physical Education Club', 'code' => 'BPEC'],
                ['name' => 'Confederation of Multiskilled Educators on Technology and Innovation', 'code' => 'COMETI'],
                ['name' => 'League of Math Wizards', 'code' => 'LMW'],
                ['name' => 'Pampamantasang Samahan ng mga Tagapagtaguyod ng Wikang Filipino', 'code' => 'PSTWF'],
                ['name' => 'Society of Elementary Educators', 'code' => 'SEE'],
                ['name' => 'The Alliance of Social Science Educators', 'code' => 'TASSE'],
                ['name' => 'Writers\' Guild', 'code' => 'WG'],
                ['name' => 'ISU SHS Student Council', 'code' => 'SHSSC'],
                ['name' => 'Student Body Organization-College of Education', 'code' => 'SBO-CED'],
            ],

            // College of Business, Accountancy and Public Administration
            'CBAPA' => [
                ['name' => 'Junior Philippine Institute of Accountants', 'code' => 'JPIA'],
                ['name' => 'Student Body Organization-College of Business, Accountancy and Public Administration', 'code' => 'SBO-CBAPA'],
            ],

            // College of Computing Studies, Information and Communication Technology
            'CCSICT' => [
                ['name' => 'ACM Student Chapter', 'code' => 'ACM'],
                ['name' => 'Student Body Organization-College of Computing Studies, Information and Communication Technology', 'code' => 'SBO-CCSICT'],
            ],

            // College of Criminal Justice Education
            'CCJE' => [
                ['name' => 'Student Body Organization-College of Criminal Justice Education', 'code' => 'SBO-CCJE'],
            ],

            // College of Engineering
            'COE' => [
                ['name' => 'Institute of Electronics Engineers of the Philippines-Student Chapter', 'code' => 'IECEP-SC'],
                ['name' => 'Student Body Organization-College of Engineering', 'code' => 'SBO-COE'],
            ],

            // College of Nursing
            'CON' => [
                ['name' => 'Student Body Organization-College of Nursing', 'code' => 'SBO-CON'],
            ],

            // Institute of Fisheries
            'IF' => [
                ['name' => 'Student Body Organization-Institute of Fisheries', 'code' => 'SBO-IF'],
            ],

            // Central Graduate School
            'CGS' => [
                ['name' => 'Graduate Students Association', 'code' => 'GSA'],
            ],
        ];

        // Seed campus-wide orgs
        foreach ($orgs['campus'] as $org) {
            Organization::firstOrCreate(
                ['name' => $org['name']],
                [
                    'name' => $org['name'],
                    'code' => $org['code'],
                    'department_id' => null,
                ]
            );
        }
        unset($orgs['campus']);

        // Seed college/unit orgs
        foreach ($orgs as $deptCode => $organizations) {
            $dept = Department::where('code', $deptCode)->first();
            if (! $dept) {
                continue;
            }

            foreach ($organizations as $org) {
                Organization::firstOrCreate(
                    ['name' => $org['name']],
                    [
                        'name' => $org['name'],
                        'code' => $org['code'],
                        'department_id' => $dept->id,
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $coe = Department::where('code', 'COE')->first();
        $cas = Department::where('code', 'CAS')->first();

        $acm = Organization::where('code', 'ACM')->first()
            ?? Organization::create(['name' => 'ACM Student Chapter', 'code' => 'ACM', 'department_id' => $coe?->id]);

        $ieee = Organization::where('code', 'IEEE')->first()
            ?? Organization::create(['name' => 'IEEE Student Branch', 'code' => 'IEEE', 'department_id' => $coe?->id]);

        $ssc = Organization::where('code', 'SSC')->first();
        $englishClub = Organization::where('code', 'ENG')->first();
        $forum = Organization::where('code', 'TFP')->first();

        $this->createAdmins($coe, $cas, $acm, $ieee, $ssc, $englishClub, $forum);

        $this->createStudentsInBatches(1000);

        $this->command->line('✅ Users seeded: ' . User::count());
    }

    private function createAdmins($coe, $cas, $acm, $ieee, $ssc, $englishClub, $forum)
    {
        $admins = [
            ['email' => 'admin@aimisu.edu.ph', 'name' => 'System Admin', 'role' => 'admin'],
            ['email' => 'orgadmin@aimisu.edu.ph', 'name' => 'ACM Admin', 'role' => 'org_admin'],
            ['email' => 'ieeeadmin@aimisu.edu.ph', 'name' => 'IEEE Admin', 'role' => 'org_admin'],
            ['email' => 'englishadmin@aimisu.edu.ph', 'name' => 'English Club Admin', 'role' => 'org_admin'],
            ['email' => 'sscadmin@aimisu.edu.ph', 'name' => 'SSC Admin', 'role' => 'org_admin'],
            ['email' => 'forumadmin@aimisu.edu.ph', 'name' => 'Forum Admin', 'role' => 'org_admin'],
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('password'),
                    'role' => $admin['role'],
                    'phone' => null,
                    'profile_photo_url' => null,
                ]
            );
        }

        $this->command->line("Created " . count($admins) . " admin/org users");
    }

    private function createStudentsInBatches($total)
    {
        $departments = Department::pluck('id')->toArray();
        $organizations = Organization::pluck('id')->toArray();

        if (empty($departments) || empty($organizations)) {
            $this->command->warn('No departments or organizations found. Create them first.');
            return;
        }

        $batchSize = 100;
        $password = Hash::make('password');

        for ($i = 0; $i < $total; $i += $batchSize) {
            $users = [];
            $remaining = min($batchSize, $total - $i);

            for ($j = 0; $j < $remaining; $j++) {
                $users[] = [
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'password' => $password,
                    'role' => 'user',
                    'phone' => fake()->phoneNumber(),
                    'profile_photo_url' => null,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            User::insert($users);
            $this->command->line("✅ Inserted users " . ($i + $remaining) . "/$total");
        }
    }
}

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

        $acm = Organization::where('name', 'ACM Student Chapter')->first()
            ?? Organization::create([
                'name' => 'ACM Student Chapter',
                'code' => 'ACM',
                'department_id' => $coe?->id,
            ]);

        $ieee = Organization::where('name', 'IEEE Student Branch')->first()
            ?? Organization::create([
                'name' => 'IEEE Student Branch',
                'code' => 'IEEE',
                'department_id' => $coe?->id,
            ]);

        $ssc = Organization::where('name', 'Supreme Student Council')->first();
        $englishClub = Organization::where('name', 'English Club')->first();
        $forum = Organization::where('name', 'The Forum Publication')->first();

        // Admin
        User::firstOrCreate(
            ['email' => 'admin@aimisu.edu.ph'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department_id' => null,
                'organization_id' => null,
            ]
        );

        // Org Admins
        User::firstOrCreate(
            ['email' => 'orgadmin@aimisu.edu.ph'],
            [
                'name' => 'ACM Admin',
                'password' => Hash::make('password'),
                'role' => 'org_admin',
                'department_id' => $coe?->id,
                'organization_id' => $acm?->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'ieeeadmin@aimisu.edu.ph'],
            [
                'name' => 'IEEE Admin',
                'password' => Hash::make('password'),
                'role' => 'org_admin',
                'department_id' => $coe?->id,
                'organization_id' => $ieee?->id,
            ]
        );

        if ($englishClub) {
            User::firstOrCreate(
                ['email' => 'englishadmin@aimisu.edu.ph'],
                [
                    'name' => 'English Club Admin',
                    'password' => Hash::make('password'),
                    'role' => 'org_admin',
                    'department_id' => $cas?->id,
                    'organization_id' => $englishClub->id,
                ]
            );
        }

        if ($ssc) {
            User::firstOrCreate(
                ['email' => 'sscadmin@aimisu.edu.ph'],
                [
                    'name' => 'SSC Admin',
                    'password' => Hash::make('password'),
                    'role' => 'org_admin',
                    'department_id' => null,
                    'organization_id' => $ssc->id,
                ]
            );
        }

        if ($forum) {
            User::firstOrCreate(
                ['email' => 'forumadmin@aimisu.edu.ph'],
                [
                    'name' => 'The Forum Publication Admin',
                    'password' => Hash::make('password'),
                    'role' => 'org_admin',
                    'department_id' => null,          // campus-wide
                    'organization_id' => $forum->id,
                ]
            );
        }

        // Regular Users
        User::firstOrCreate(
            ['email' => 'user@aimisu.edu.ph'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department_id' => $coe?->id,
                'organization_id' => $acm?->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user1@aimisu.edu.ph'],
            [
                'name' => 'CAS User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department_id' => $cas?->id,
                'organization_id' => $englishClub?->id,
            ]
        );
    }
}

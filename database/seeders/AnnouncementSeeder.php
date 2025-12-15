<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        Announcement::firstOrCreate(
            ['title' => 'Welcome to ISU Portal'],
            [
                'content' => 'This is the new Aimisu campus event and announcement portal.',
                'organization_id' => 1,
                'created_by' => 1,
                'status' => 'published',
                'published_at' => now(),
            ]
        );
    }
}

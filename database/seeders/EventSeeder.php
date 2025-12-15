<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();
        $locations = Location::all();
        $users = User::where('role', 'org_admin')->get();

        if ($organizations->isEmpty() || $locations->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Missing organizations, locations, or users.');
            return;
        }

        $titles = [
            'Welcome Freshman Orientation',
            'Tech Conference 2025',
            'Sports Day Championship',
            'Cultural Fiesta',
            'Research Symposium',
            'Alumni Networking Night',
            'Career Fair',
            'Hackathon Challenge',
            'Academic Seminar Series',
            'Student Leadership Summit',
            'Arts Exhibition Opening',
            'Science Fair 2025',
            'Photography Workshop',
            'Film Festival',
            'Health & Wellness Fair',
            'Debate Championship',
            'Music Concert Series',
            'Entrepreneurship Summit',
        ];

        $descriptions = [
            'Join us for an exciting orientation program for new students.',
            'A comprehensive conference featuring the latest in technology.',
            'Compete in various sports competitions and enjoy the festivities.',
            'Celebrate diverse cultures with performances and food.',
            'Showcase groundbreaking research from our departments.',
            'Network with successful alumni and industry professionals.',
            'Meet top companies recruiting for graduates.',
            'Team up and build amazing projects in 48 hours.',
            'Expert speakers discussing current academic topics.',
            'Learn leadership skills from industry leaders.',
            'View exceptional student artwork and installations.',
            'Students present their scientific experiments.',
            'Learn mobile and DSLR photography basics.',
            'Showcase of independent and student-made films.',
            'Mental health awareness and wellness consultations.',
            'Inter-collegiate debate tournament finals.',
            'Live performances from student and local artists.',
            'Networking and pitch competition for startups.',
        ];

        $categories = ['academic', 'sports', 'cultural', 'social', 'other'];

        $eventCount = 0;
        for ($dayOffset = -60; $dayOffset <= 60; $dayOffset += 3) {
            $startDate = Carbon::now()->addDays($dayOffset);
            $endDate = $startDate->copy()->addDays(rand(0, 2));

            $creator = $users->random();
            $org = $organizations->firstWhere('id', $creator->organization_id) ?? $organizations->random();
            $location = $locations->random();

            $dailyTimes = [];
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $startTime = sprintf('%02d:00', rand(9, 17));
                $endTime = sprintf('%02d:00', rand(18, 20));
                $dailyTimes[$current->format('Y-m-d')] = "{$startTime}-{$endTime}";
                $current->addDay();
            }

            $status = $dayOffset < 0 ? 'published' : 'pending_approval';

            Event::create([
                'title' => $titles[array_rand($titles)],
                'description' => $descriptions[array_rand($descriptions)],
                'category' => $categories[array_rand($categories)],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'daily_times' => $dailyTimes,
                'location_id' => $location->id,
                'organization_id' => $org->id,
                'created_by' => $creator->id,
                'status' => $status,
                'published_at' => $status === 'published' ? $startDate->copy()->subDays(14) : null,
                'created_at' => $startDate->copy()->subDays(rand(14, 30)),
            ]);

            $eventCount++;
        }

        $this->command->line("✅ Created $eventCount events across 4 months");
    }
}

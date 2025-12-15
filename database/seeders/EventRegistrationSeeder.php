<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\EventRegistration;
use Illuminate\Database\Seeder;

class EventRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::where('status', 'published')->get();
        $allUsers = User::where('role', 'user')->get();

        if ($events->isEmpty() || $allUsers->isEmpty()) {
            $this->command->warn('No published events or students found.');
            return;
        }

        foreach ($events as $event) {
            // BIGGER registration numbers per event
            if ($event->category === 'cultural') {
                $registrationCount = rand(300, 500);
            } elseif ($event->category === 'sports') {
                $registrationCount = rand(200, 400);
            } elseif ($event->category === 'academic') {
                $registrationCount = rand(100, 250);
            } else {
                $registrationCount = rand(80, 200);
            }

            $count = min($registrationCount, $allUsers->count());
            $randomUsers = $allUsers->random($count);

            $registeredCount = 0;
            foreach ($randomUsers as $user) {
                $daysBeforeEvent = rand(1, 14);
                $registeredAt = $event->start_date->copy()->subDays($daysBeforeEvent);

                EventRegistration::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'status' => 'registered',
                        'created_at' => $registeredAt,
                        'updated_at' => $registeredAt,
                    ]
                );
                $registeredCount++;
            }

            $this->command->line("Event '{$event->title}': {$registeredCount} registrations");
        }
    }
}

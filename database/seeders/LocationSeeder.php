<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            [
                'name' => 'ISU Main Auditorium',
                'latitude' => 16.6916,
                'longitude' => 121.6774,
                'description' => 'Main auditorium for large campus events and assemblies.',
            ],
            [
                'name' => 'Sports Field',
                'latitude' => 16.6930,
                'longitude' => 121.6788,
                'description' => 'Outdoor field used for intramurals and sports festivals.',
            ],
            [
                'name' => 'Engineering Lobby',
                'latitude' => 16.6921,
                'longitude' => 121.6765,
                'description' => 'Lobby area of the College of Engineering building.',
            ],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(
                ['name' => $loc['name']],
                $loc
            );
        }
    }
}

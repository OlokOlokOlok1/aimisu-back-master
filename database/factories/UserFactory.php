<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $departments = Department::pluck('id')->toArray();
        $organizations = Organization::pluck('id')->toArray();

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'student',
            'department_id' => $this->faker->randomElement($departments),
            'organization_id' => $this->faker->randomElement($organizations),
            'remember_token' => Str::random(10),
        ];
    }
}

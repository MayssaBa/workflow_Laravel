<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->sentence(),
            'manager_id' => User::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $startDate->modify('+14 days')->format('Y-m-d'),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\TaskHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskHistoryFactory extends Factory
{
    /** @var string */
    protected $model = TaskHistory::class;

    public function definition(): array
    {
        return [
            'action' => fn () => fake()->randomElement(['created', 'assigned', 'reorganized', 'closed']),
            'old_value' => null,
            'new_value' => $this->faker->sentence,
        ];
    }
}
?>

namespace Database\Factories;

use App\Models\TaskHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskHistory>
 */
class TaskHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}

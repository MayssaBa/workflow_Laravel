<?php

namespace Database\Factories;

use App\Models\TaskHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskHistory>
 */
class TaskHistoryFactory extends Factory
{
    /** @var string */
    protected $model = TaskHistory::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}

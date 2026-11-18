<?php

namespace Database\Factories;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    /** @var string */
    protected $model = Issue::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->text,
            'status' => fn () => fake()->randomElement(['open', 'resolved']),
            'resolved_by' => rand(1, 10),
        ];
    }
}
?>

namespace Database\Factories;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Issue>
 */
class IssueFactory extends Factory
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

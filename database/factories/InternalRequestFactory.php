<?php

namespace Database\Factories;

use App\Models\InternalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class InternalRequestFactory extends Factory
{
    /** @var string */
    protected $model = InternalRequest::class;

    public function definition(): array
    {
        return [
            'sender_id' => rand(1, 10),
            'recipient_id' => rand(1, 10),
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'status' => fn () => fake()->randomElement(['open', 'answered', 'closed']),
        ];
    }
}
?>

namespace Database\Factories;

use App\Models\InternalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InternalRequest>
 */
class InternalRequestFactory extends Factory
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

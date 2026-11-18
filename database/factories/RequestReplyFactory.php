<?php

namespace Database\Factories;

use App\Models\RequestReply;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestReplyFactory extends Factory
{
    /** @var string */
    protected $model = RequestReply::class;

    public function definition(): array
    {
        return [
            'internal_request_id' => rand(1, 50),
            'user_id' => rand(1, 10),
            'body' => $this->faker->paragraph,
        ];
    }
}
?>

namespace Database\Factories;

use App\Models\RequestReply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestReply>
 */
class RequestReplyFactory extends Factory
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

<?php

namespace Database\Factories;

use App\Models\RequestReply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestReply>
 */
class RequestReplyFactory extends Factory
{
    /** @var string */
    protected $model = RequestReply::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'body' => fake()->paragraph(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\InternalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InternalRequest>
 */
class InternalRequestFactory extends Factory
{
    /** @var string */
    protected $model = InternalRequest::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => 'open',
        ];
    }
}

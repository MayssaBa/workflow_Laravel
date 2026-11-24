<?php

namespace Database\Factories;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Issue>
 */
class IssueFactory extends Factory
{
    /** @var string */
    protected $model = Issue::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'status' => 'open',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Poll>
 */
class PollFactory extends Factory
{
    protected $model = Poll::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'question' => $this->faker->sentence(8),
            'slug' => Str::uuid()->toString(),
            'allow_multiple' => false,
        ];
    }
}



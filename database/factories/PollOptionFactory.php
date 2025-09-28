<?php

namespace Database\Factories;

use App\Models\PollOption;
use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PollOption>
 */
class PollOptionFactory extends Factory
{
    protected $model = PollOption::class;

    public function definition(): array
    {
        return [
            'poll_id' => Poll::factory(),
            'option_text' => $this->faker->words(2, true),
        ];
    }
}



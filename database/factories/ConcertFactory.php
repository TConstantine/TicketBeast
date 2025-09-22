<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Concert>
 */
class ConcertFactory extends Factory
{

    #[Override]
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'subtitle' => $this->faker->sentence(),
            'date' => Carbon::parse($this->faker->dateTimeBetween('+1 days', '+1 year')),
            'ticket_price' => $this->faker->numberBetween(10),
            'venue' => $this->faker->sentence(),
            'venue_address' => $this->faker->sentence(),
            'city' => $this->faker->sentence(),
            'state' => $this->faker->sentence(),
            'zip' => $this->faker->sentence(),
            'additional_information' => $this->faker->sentence()
        ];
    }

    public function published(): static
    {
        return $this->state(fn() => [
                    'published_at' => Carbon::parse('-1 week')
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn() => [
                    'published_at' => null
        ]);
    }
}

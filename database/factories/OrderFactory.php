<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{

    #[Override]
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(10),
            'email' => $this->faker->safeEmail()
        ];
    }
}

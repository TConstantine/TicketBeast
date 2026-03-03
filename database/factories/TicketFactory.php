<?php

namespace Database\Factories;

use App\Models\Concert;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{

    #[Override]
    public function definition(): array
    {
        return [
            'concert_id' => function () {
                return Concert::factory()->published()->create();
            }
        ];
    }
}

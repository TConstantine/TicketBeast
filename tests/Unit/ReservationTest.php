<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReservationTest extends TestCase
{

    #[Test]
    public function totalCostIsCalculated(): void
    {
        $tickets = new Collection([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200]
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

    #[Test]
    public function reservedTicketsCanBeRetrieved(): void
    {
        $tickets = new Collection([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200]
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals($tickets, $reservation->tickets());
    }

    #[Test]
    public function reservedTicketsAreReleasedWhenReservationIsCancelled(): void
    {
        $tickets = new Collection([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class)
        ]);
        $reservation = new Reservation($tickets);

        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }
}

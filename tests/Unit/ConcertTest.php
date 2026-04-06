<?php

namespace Tests\Unit;

use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConcertTest extends TestCase
{

    use DatabaseMigrations;

    #[Test]
    public function dateIsFormatted(): void
    {
        $concert = Concert::factory()->make(['date' => Carbon::parse('2016-12-01 8:00pm')]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    #[Test]
    public function startTimeIsFormatted(): void
    {
        $concert = Concert::factory()->make(['date' => Carbon::parse('2016-12-01 17:00:00')]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    #[Test]
    public function ticketPriceIsInDollars(): void
    {
        $concert = Concert::factory()->make(['ticket_price' => 6750]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    #[Test]
    public function concertsWithPublishedAtDateArePublished(): void
    {
        $publishedConcertA = Concert::factory()->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB = Concert::factory()->create(['published_at' => Carbon::parse('-1 week')]);
        $unpublishedConcert = Concert::factory()->create(['published_at' => null]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    #[Test]
    public function canAddTickets(): void
    {
        $concert = Concert::factory()->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    #[Test]
    public function ticketsRemainingDoesNotIncludeTicketsAssociatedWithOrder(): void
    {
        $concert = Concert::factory()->create();
        $concert->tickets()->saveMany(Ticket::factory(30)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(Ticket::factory(20)->create(['order_id' => null]));

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    #[Test]
    public function throwsExceptionWhenTryingToReserveMoreTicketsThanRemaining(): void
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(10);
        try {
            $concert->reserveTickets(11, 'jane@example.com');
            $this->fail('Expected NotEnoughTicketsException was not thrown.');
        } catch (NotEnoughTicketsException $exception) {
            $order = $concert->orders()->where('email', 'jane@example.com')->first();
            $this->assertNull($order);
            $this->assertEquals(10, $concert->ticketsRemaining());
        }
    }

    #[Test]
    public function availableTicketsCanBeReserved(): void
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservation = $concert->reserveTickets(2, 'john@example.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@example.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    #[Test]
    public function purchasedTicketsCannotBeReserved(): void
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $order = Order::factory()->create();
        $order->tickets()->saveMany($concert->tickets->take(2));
        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $exception) {
            $this->assertEquals(1, $concert->ticketsRemaining());
        }
    }

    #[Test]
    public function reservedTicketsCannotBeReserved(): void
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $concert->reserveTickets(2, 'jane@example.com');
        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $exception) {
            $this->assertEquals(1, $concert->ticketsRemaining());
        }
    }
}

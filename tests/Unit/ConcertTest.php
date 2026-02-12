<?php

namespace Tests\Unit;

use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
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
    public function canOrderConcertTickets(): void
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(3);

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
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
        $concert->addTickets(50);
        $concert->orderTickets('jane@example.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    #[Test]
    public function throwsExceptionWhenTryingToPurchaseMoreTicketsThanRemaining(): void
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(10);
        try {
            $concert->orderTickets('jane@example.com', 11);
            $this->fail('Expected NotEnoughTicketsException was not thrown.');
        } catch (NotEnoughTicketsException $exception) {
            $order = $concert->orders()->where('email', 'jane@example.com')->first();
            $this->assertNull($order);
            $this->assertEquals(10, $concert->ticketsRemaining());
        }
    }

    #[Test]
    public function cannotOrderTicketsThatHaveAlreadyBeenPurchased(): void
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(10);
        $concert->orderTickets('jane@example.com', 8);
        try {
            $concert->orderTickets('john@example.com', 3);
            $this->fail('Expected NotEnoughTicketsException was not thrown.');
        } catch (NotEnoughTicketsException $exception) {
            $order = $concert->orders()->where('email', 'john@example.com')->first();
            $this->assertNull($order);
            $this->assertEquals(2, $concert->ticketsRemaining());
        }
    }
}

<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Ticket;
use App\TicketCodeGeneratorInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\Stub\FakeTicketCodeGenerator;
use Tests\TestCase;

class TicketTest extends TestCase
{

    use DatabaseMigrations;

    #[Test]
    public function it_can_be_reserved(): void
    {
        $ticket = Ticket::factory()->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    #[Test]
    public function it_can_be_released(): void
    {
        $ticket = Ticket::factory()->reserved()->create();
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

    #[Test]
    public function it_can_be_claimed_for_an_order(): void
    {
        $order = Order::factory()->create();
        $ticket = Ticket::factory()->create(['code' => null]);
        $ticketCodeGenerator = new FakeTicketCodeGenerator();
        $this->app->instance(TicketCodeGeneratorInterface::class, $ticketCodeGenerator);

        $ticket->claimFor($order);

        $this->assertContains($ticket->id, $order->tickets->pluck('id'));
        $this->assertEquals('TICKETCODE1', $ticket->code);
    }
}

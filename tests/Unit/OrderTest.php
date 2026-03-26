<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderTest extends TestCase
{

    use DatabaseMigrations;

    #[Test]
    public function orderIsCreatedFromTicketsEmailAndAmount(): void
    {
        $concert = Concert::factory()->published()->create()->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    #[Test]
    public function orderCanBeRetrievedByConfirmationNumber(): void
    {
        $order = Order::factory()->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $retrievedOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');

        $this->assertEquals($order->id, $retrievedOrder->id);
    }

    #[Test]
    public function exceptionIsThrownWhenOrderWithGivenConfirmationNumberDoesNotExist(): void
    {
        $this->expectException(ModelNotFoundException::class);
        
        Order::findByConfirmationNumber('NONEXISTENTCONFIRMATIONNUMBER');
    }

    #[Test]
    public function orderIsConvertedToArray(): void
    {
        $order = Order::factory()->create([
            'confirmation_number' => 'AHQ8VVDT58CKQDZPLQS4XW88',
            'email' => 'jane@example.com',
            'amount' => 6000
        ]);
        $order->tickets()->saveMany(Ticket::factory()->times(5)->create());

        $result = $order->toArray();

        $this->assertEquals([
            'confirmation_number' => 'AHQ8VVDT58CKQDZPLQS4XW88',
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000
                ], $result);
    }
}

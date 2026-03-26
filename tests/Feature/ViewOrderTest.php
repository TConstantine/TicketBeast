<?php

namespace Tests\Feature;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{

    use DatabaseMigrations;

    #[Test]
    public function userCanViewTheirOrderConfirmation(): void
    {
        $concert = Concert::factory()->published()->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('March 12, 2017 8:00pm'),
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916'
        ]);
        $order = Order::factory()->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four' => '1881',
            'email' => 'john@example.com',
            'amount' => 8500
        ]);
        Ticket::factory()->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE123'
        ]);
        Ticket::factory()->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE456'
        ]);
        
        $response = $this->get('/orders/ORDERCONFIRMATION1234');
        
        $response->assertStatus(200);
        $response->assertViewHas('order', $order);
        $response->assertSee('ORDERCONFIRMATION1234');
        $response->assertSee('$85.00');
        $response->assertSee('**** **** **** 1881');
        $response->assertSee('TICKETCODE123');
        $response->assertSee('TICKETCODE456');
        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville, ON');
        $response->assertSee('17916');
        $response->assertSee('john@example.com');
        $response->assertSee('2017-03-12 20:00');
    }
}

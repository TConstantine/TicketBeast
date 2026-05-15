<?php

use App\Mail\OrderConfirmationEmail;
use App\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderConfirmationEmailTest extends TestCase
{

    #[Test]
    public function it_contains_a_link_to_order_confirmation_page(): void
    {
        $order = Order::factory()->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $email = new OrderConfirmationEmail($order);

        $this->assertStringContainsString(url('/orders/ORDERCONFIRMATION1234'), $email->render());
    }

    #[Test]
    public function it_has_a_subject(): void
    {
        $order = Order::factory()->make();

        $email = new OrderConfirmationEmail($order);

        $this->assertTrue($email->hasSubject('Your TicketBeast Order'));
    }
}

<?php

namespace Tests\Unit\Billing;

use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Stripe\StripeClient;

class StripePaymentGatewayTest extends TestCase
{

    #[Test]
    public function chargesWithValidPaymentTokenAreSuccessful(): void
    {
        $client = new StripeClient(config('services.stripe.secret'));
        $paymentGateway = new StripePaymentGateway($client);

        $paymentIntent = $paymentGateway->charge(2500, 'pm_card_visa');

        $this->assertEquals(2500, $paymentIntent->amount);
        $this->assertEquals('succeeded', $paymentIntent->status);
        $this->assertCount(1, $client->charges->all(['limit' => 1])['data']);
    }

    #[Test]
    public function chargesWithInvalidPaymentTokenFail(): void
    {
        $client = new StripeClient(config('services.stripe.secret'));
        $paymentGateway = new StripePaymentGateway($client);

        $this->expectException(PaymentFailedException::class);

        $paymentGateway->charge(2500, 'invalid-payment-token');
        $this->assertCount(0, $client->charges->all(['limit' => 1])['data']);
    }
}

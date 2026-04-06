<?php

namespace Tests\Unit\Billing;

use App\Billing\PaymentGatewayInterface;
use App\Billing\StripePaymentGateway;
use Illuminate\Foundation\Testing\TestCase;
use Override;
use Stripe\StripeClient;

class StripePaymentGatewayTest extends TestCase
{

    use PaymentGatewayContractTests;

    private StripeClient $client;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    #[Override]
    protected function getPaymentGateway(): PaymentGatewayInterface
    {
        return new StripePaymentGateway($this->client);
    }
}

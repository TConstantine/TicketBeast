<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FakePaymentGatewayTest extends TestCase
{

    #[Test]
    public function chargesWithValidPaymentTokenAreSuccessful(): void
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    #[Test]
    public function chargesWithInvalidPaymentTokenFail(): void
    {
        $paymentGateway = new FakePaymentGateway;

        $this->expectException(PaymentFailedException::class);

        $paymentGateway->charge(2500, 'invalid-payment-token');
    }
}

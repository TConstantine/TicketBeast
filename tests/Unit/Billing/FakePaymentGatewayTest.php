<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\TestCase;
use Override;
use PHPUnit\Framework\Attributes\Test;

class FakePaymentGatewayTest extends TestCase
{

    use PaymentGatewayContractTests;

    #[Test]
    public function hookRunsBeforeFirstCharge(): void
    {
        $paymentGateway = $this->getPaymentGateway();
        $timesCallbackRan = 0;

        $paymentGateway->beforeFirstCharge(function (FakePaymentGateway $paymentGateway) use (&$timesCallbackRan) {
            $timesCallbackRan++;
            $paymentGateway->charge(2500, $paymentGateway->getValidToken());
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidToken());

        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
    }

    #[Override]
    protected function getPaymentGateway(): PaymentGatewayInterface
    {
        return new FakePaymentGateway();
    }
}

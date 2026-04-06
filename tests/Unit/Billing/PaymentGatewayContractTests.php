<?php

namespace Tests\Unit\Billing;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGatewayInterface;
use PHPUnit\Framework\Attributes\Test;

trait PaymentGatewayContractTests
{

    abstract protected function getPaymentGateway(): PaymentGatewayInterface;

    #[Test]
    public function chargesWithValidPaymentTokenAreSuccessful(): void
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidToken());
        });

        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->sum->amount());
    }

    #[Test]
    public function detailsAreReturnedAboutSuccessfulCharge(): void
    {
        $paymentGateway = $this->getPaymentGateway();

        $charge = $paymentGateway->charge(2500, $paymentGateway->getValidToken('0000000000004242'));

        $this->assertEquals('4242', $charge->cardLastFour());
        $this->assertEquals(2500, $charge->amount());
    }

    #[Test]
    public function chargesWithInvalidPaymentTokenFail(): void
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            try {
                $paymentGateway->charge(2500, 'invalid-payment-token');
            } catch (PaymentFailedException $e) {
                return;
            }

            $this->fail('Charging with an invalid payment token did not throw a PaymentFailedException.');
        });

        $this->assertCount(0, $newCharges);
    }

    #[Test]
    public function canFetchChargesCreatedDuringCallback(): void
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(2000, $paymentGateway->getValidToken());
        $paymentGateway->charge(3000, $paymentGateway->getValidToken());

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidToken());
            $paymentGateway->charge(5000, $paymentGateway->getValidToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([5000, 4000], $newCharges->map->amount()->all());
    }
}

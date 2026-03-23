<?php

namespace App\Billing;

use Closure;
use Illuminate\Support\Collection;
use Override;
use Stripe\PaymentIntent;

class FakePaymentGateway implements PaymentGatewayInterface
{

    private Collection $charges;
    private ?Closure $beforeFirstChargeCallback = null;

    public function __construct()
    {
        $this->charges = collect();
    }

    #[Override]
    public function charge(int $amount, string $paymentMethod): PaymentIntent
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }
        if ($paymentMethod !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }
        $this->charges[] = $amount;
        return new PaymentIntent();
    }

    public function getValidTestToken(): string
    {
        return 'valid-token';
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

    public function beforeFirstCharge(Closure $callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}

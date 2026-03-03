<?php

namespace App\Billing;

use Closure;
use Illuminate\Support\Collection;
use Override;

class FakePaymentGateway implements PaymentGatewayInterface
{

    private Collection $charges;
    private ?Closure $beforeFirstChargeCallback = null;

    public function __construct()
    {
        $this->charges = collect();
    }

    #[Override]
    public function charge(int $amount, string $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }
        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }
        $this->charges[] = $amount;
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

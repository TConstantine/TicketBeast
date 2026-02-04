<?php

namespace App\Billing;

use Illuminate\Support\Collection;
use Override;

class FakePaymentGateway implements PaymentGatewayInterface
{

    private Collection $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    #[Override]
    public function charge(int $amount, string $token)
    {
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
}

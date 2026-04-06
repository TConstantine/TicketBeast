<?php

namespace App\Billing;

use Closure;
use Illuminate\Support\Collection;
use Override;

class FakePaymentGateway implements PaymentGatewayInterface
{

    private Collection $charges;
    private Collection $tokens;
    private ?Closure $beforeFirstChargeCallback = null;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    #[Override]
    public function charge(int $amount, string $paymentMethod): Charge
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }
        if (!$this->tokens->has($paymentMethod)) {
            throw new PaymentFailedException;
        }
        $charge = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$paymentMethod], -4)
        ]);
        $this->charges[] = $charge;
        return $charge;
    }

    #[Override]
    public function getValidToken(string $cardNumber = '4242424242424242'): string
    {
        $token = 'fake-tok_' . str()->random(24);
        $this->tokens[$token] = $cardNumber;
        return $token;
    }

    #[Override]
    public function newChargesDuring(callable $callback): Collection
    {
        $chargesFrom = $this->charges->count();
        $callback($this);
        return $this->charges->slice($chargesFrom)->reverse()->values();
    }

    public function totalCharges(): int
    {
        return $this->charges->sum->amount();
    }

    public function beforeFirstCharge(Closure $callback): void
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}

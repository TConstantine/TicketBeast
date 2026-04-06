<?php

namespace App\Billing;

use Illuminate\Support\Collection;

interface PaymentGatewayInterface
{
    public function charge(int $amount, string $paymentMethod): Charge;

    public function getValidToken(string $cardNumber = ''): string;

    public function newChargesDuring(callable $callback): Collection;
}
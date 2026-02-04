<?php

namespace App\Billing;

interface PaymentGatewayInterface
{

    public function charge(int $amount, string $token);
}

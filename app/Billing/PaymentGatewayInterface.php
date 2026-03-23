<?php

namespace App\Billing;

use Stripe\PaymentIntent;

interface PaymentGatewayInterface
{

    public function charge(int $amount, string $paymentMethod): PaymentIntent;
}

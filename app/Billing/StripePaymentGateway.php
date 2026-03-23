<?php

namespace App\Billing;

use Override;
use Stripe\Exception\InvalidRequestException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripePaymentGateway implements PaymentGatewayInterface
{

    public function __construct(
            private StripeClient $client
    )
    {
        
    }

    #[Override]
    public function charge(int $amount, string $paymentMethod): PaymentIntent
    {
        try {
            return $this->client->paymentIntents->create([
                        'amount' => $amount,
                        'currency' => 'eur',
                        'payment_method' => $paymentMethod,
                        'payment_method_types' => ['card'],
                        'confirm' => true
            ]);
        } catch (InvalidRequestException $exception) {
            throw new PaymentFailedException;
        }
    }
}

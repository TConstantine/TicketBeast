<?php

namespace App\Billing;

use Illuminate\Support\Collection;
use Override;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class StripePaymentGateway implements PaymentGatewayInterface
{

    public function __construct(
            private StripeClient $client
    )
    {
        
    }

    #[Override]
    public function charge(int $amount, string $paymentMethod): Charge
    {
        try {
            $paymentIntent = $this->client->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method' => $paymentMethod,
                'payment_method_types' => ['card'],
                'confirm' => true
            ]);
            $stripeCharge = $this->client->charges->retrieve($paymentIntent->latest_charge);
            return new Charge([
                'amount' => $paymentIntent->amount,
                'card_last_four' => $stripeCharge->payment_method_details->card->last4,
            ]);
        } catch (InvalidRequestException $exception) {
            throw new PaymentFailedException;
        }
    }

    #[Override]
    public function getValidToken(string $cardNumber = ''): string
    {
        return 'pm_card_visa';
    }

    #[Override]
    public function newChargesDuring(callable $callback): Collection
    {
        $latestChargeBefore = $this->lastStripeChargeId();
        $callback($this);
        return $this->newChargesSince($latestChargeBefore);
    }

    private function lastStripeChargeId(): ?string
    {
        $charges = $this->client->charges->all(['limit' => 1]);
        return $charges->data[0]->id ?? null;
    }

    private function newChargesSince(?string $chargeId): Collection
    {
        $params = ['limit' => 100];
        if ($chargeId !== null) {
            $params['ending_before'] = $chargeId;
        }
        return collect(
                        $this->client->charges->all($params)->data
                )->map(function ($stripeCharge) {
                    return new Charge([
                        'amount' => $stripeCharge->amount,
                        'card_last_four' => $stripeCharge->payment_method_details->card->last4,
                    ]);
                });
    }
}

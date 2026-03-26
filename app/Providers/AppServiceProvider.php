<?php

namespace App\Providers;

use App\Billing\PaymentGatewayInterface;
use App\Billing\StripePaymentGateway;
use App\ConfirmationNumberGeneratorInterface;
use App\OrderConfirmationNumberGenerator;
use Illuminate\Support\ServiceProvider;
use Override;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{

    #[Override]
    public function register(): void
    {
        $this->app->bind(StripePaymentGateway::class, function () {
            $client = new StripeClient(config('services.stripe.secret'));
            return new StripePaymentGateway($client);
        });
        $this->app->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);
        $this->app->bind(ConfirmationNumberGeneratorInterface::class, OrderConfirmationNumberGenerator::class);
    }

    public function boot(): void
    {
        
    }
}

<?php

namespace App\Providers;

use App\Billing\PaymentGatewayInterface;
use App\Billing\StripePaymentGateway;
use App\ConfirmationNumberGeneratorInterface;
use App\HashidsTicketCodeGenerator;
use App\OrderConfirmationNumberGenerator;
use App\TicketCodeGeneratorInterface;
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
        $this->app->bind(HashidsTicketCodeGenerator::class, function () {
            return new HashidsTicketCodeGenerator(config('app.ticket_code_salt'));
        });

        $this->app->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);
        $this->app->bind(ConfirmationNumberGeneratorInterface::class, OrderConfirmationNumberGenerator::class);
        $this->app->bind(TicketCodeGeneratorInterface::class, HashidsTicketCodeGenerator::class);
    }

    public function boot(): void
    {
        
    }
}

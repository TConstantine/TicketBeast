<?php

namespace App;

use App\Billing\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class Reservation
{

    private Collection $tickets;
    private string $email;

    public function __construct(
            Collection $tickets,
            string $email
    )
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function totalCost(): int
    {
        return $this->tickets->sum('price');
    }

    public function tickets(): Collection
    {
        return $this->tickets;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function complete(PaymentGatewayInterface $paymentGateway, string $paymentToken): Order
    {
        $charge = $paymentGateway->charge($this->totalCost(), $paymentToken);
        return Order::forTickets($this->tickets(), $this->email(), $charge);
    }

    public function cancel(): void
    {
        $this->tickets->each(function ($ticket) {
            $ticket->release();
        });
    }
}

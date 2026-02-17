<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGatewayInterface;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
use App\Reservation;

class ConcertOrdersController extends Controller
{

    private PaymentGatewayInterface $paymentGateway;

    public function __construct(
            PaymentGatewayInterface $paymentGateway
    )
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);
        request()->validate([
            'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required']
        ]);
        try {
            $tickets = $concert->findTickets(request('ticket_quantity'));
            $reservation = new Reservation($tickets);
            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));
            $order = Order::forTickets($tickets, request('email'), $reservation->totalCost());
            return response()->json($order, 201);
        } catch (PaymentFailedException $exception) {
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $exception) {
            return response()->json([], 422);
        }
    }
}

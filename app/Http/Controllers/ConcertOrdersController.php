<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGatewayInterface;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;

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
            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));
            $order = $reservation->complete($this->paymentGateway, request('payment_token'));
            return response()->json($order, 201);
        } catch (PaymentFailedException $exception) {
            $reservation->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $exception) {
            return response()->json([], 422);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrdersController extends Controller
{

    public function show(string $confirmationNumber): View
    {
        $order = Order::findByConfirmationNumber($confirmationNumber);
        return view('orders.show', ['order' => $order]);
    }
}

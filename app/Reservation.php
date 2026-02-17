<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;

class Reservation
{

    private Collection $tickets;

    public function __construct(
            Collection $tickets
    )
    {
        $this->tickets = $tickets;
    }

    public function totalCost(): int
    {
        return $this->tickets->sum('price');
    }
}

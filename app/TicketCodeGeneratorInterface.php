<?php

namespace App;

use App\Models\Ticket;

interface TicketCodeGeneratorInterface
{

    public function generateFor(Ticket $ticket): string;
}

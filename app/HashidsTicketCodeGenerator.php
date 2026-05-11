<?php

namespace App;

use App\Models\Ticket;
use Hashids\Hashids;
use Override;

class HashidsTicketCodeGenerator implements TicketCodeGeneratorInterface
{

    private Hashids $hashids;

    public function __construct(string $salt)
    {
        $this->hashids = new Hashids($salt, 6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    #[Override]
    public function generateFor(Ticket $ticket): string
    {
        return $this->hashids->encode($ticket->id);
    }
}

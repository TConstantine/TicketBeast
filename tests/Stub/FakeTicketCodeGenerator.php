<?php

namespace Tests\Stub;

use App\Models\Ticket;
use App\TicketCodeGeneratorInterface;
use Override;

class FakeTicketCodeGenerator implements TicketCodeGeneratorInterface
{

    private int $counter = 0;

    #[Override]
    public function generateFor(Ticket $ticket): string
    {
        $this->counter++;
        return 'TICKETCODE' . $this->counter;
    }
}

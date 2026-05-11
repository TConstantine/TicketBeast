<?php

namespace Tests\Unit;

use App\HashidsTicketCodeGenerator;
use App\Models\Ticket;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HashidsTicketCodeGeneratorTest extends TestCase
{

    #[Test]
    public function ticket_code_is_at_least_6_characters_long(): void
    {
        $generator = new HashidsTicketCodeGenerator('testsalt1');

        $ticketCode = $generator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals(6, strlen($ticketCode));
    }

    #[Test]
    public function ticket_code_can_only_contain_uppercase_letters(): void
    {
        $generator = new HashidsTicketCodeGenerator('testsalt1');

        $ticketCode = $generator->generateFor(new Ticket(['id' => 1]));

        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $ticketCode);
    }

    #[Test]
    public function ticket_codes_for_the_same_ticket_id_are_the_same(): void
    {
        $generator = new HashidsTicketCodeGenerator('testsalt1');

        $ticketCode1 = $generator->generateFor(new Ticket(['id' => 1]));
        $ticketCode2 = $generator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($ticketCode1, $ticketCode2);
    }

    #[Test]
    public function ticket_codes_for_different_ticket_ids_are_different(): void
    {
        $generator = new HashidsTicketCodeGenerator('testsalt1');

        $ticketCode1 = $generator->generateFor(new Ticket(['id' => 1]));
        $ticketCode2 = $generator->generateFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($ticketCode1, $ticketCode2);
    }

    #[Test]
    public function ticket_codes_generated_with_different_salts_are_different(): void
    {
        $generator1 = new HashidsTicketCodeGenerator('testsalt1');
        $generator2 = new HashidsTicketCodeGenerator('testsalt2');

        $ticketCode1 = $generator1->generateFor(new Ticket(['id' => 1]));
        $ticketCode2 = $generator2->generateFor(new Ticket(['id' => 1]));

        $this->assertNotEquals($ticketCode1, $ticketCode2);
    }
}

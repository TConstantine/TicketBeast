<?php

namespace Tests\Unit;

use App\OrderConfirmationNumberGenerator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderConfirmationNumberGeneratorTest extends TestCase
{

    #[Test]
    public function confirmationNumberIs24CharactersLong(): void
    {
        $generator = new OrderConfirmationNumberGenerator();

        $confirmationNumber = $generator->generate();

        $this->assertEquals(24, strlen($confirmationNumber));
    }

    #[Test]
    public function confirmationNumberContainsOnlyUppercaseLettersAndNumbers(): void
    {
        $generator = new OrderConfirmationNumberGenerator();

        $confirmationNumber = $generator->generate();

        $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    #[Test]
    public function confirmationNumberCannotContainAmbiguousCharacters(): void
    {
        $generator = new OrderConfirmationNumberGenerator();

        $confirmationNumber = $generator->generate();

        $this->assertFalse(strpos($confirmationNumber, '1'));
        $this->assertFalse(strpos($confirmationNumber, 'I'));
        $this->assertFalse(strpos($confirmationNumber, '0'));
        $this->assertFalse(strpos($confirmationNumber, 'O'));
    }

    #[Test]
    public function confirmationNumbersAreUnique(): void
    {
        $generator = new OrderConfirmationNumberGenerator();
        $confirmationNumbers = array_map(function (int $i) use ($generator) {
            return $generator->generate();
        }, range(1, 100));

        $this->assertCount(100, array_unique($confirmationNumbers));
    }
}

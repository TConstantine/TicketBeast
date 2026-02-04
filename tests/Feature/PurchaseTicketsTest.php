<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGatewayInterface;
use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\TestResponse;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{

    use DatabaseMigrations;

    private FakePaymentGateway $paymentGateway;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGatewayInterface::class, $this->paymentGateway);
    }

    #[Test]
    public function customerCanPurchaseTicketsWhenConcertIsPublished(): void
    {
        $concert = Concert::factory()->published()->create([
            'ticket_price' => 3250
        ]);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(201);
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    #[Test]
    public function customerCannotPurchaseTicketsWhenConcertIsUnpublished(): void
    {
        $concert = Concert::factory()->unpublished()->create();

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    #[Test]
    public function orderIsNotCreatedWhenPaymentFails(): void
    {
        $concert = Concert::factory()->published()->create([
            'ticket_price' => 3250
        ]);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token'
        ]);

        $response->assertStatus(422);
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
    }

    #[Test]
    public function emailIsRequiredToPurchaseTickets(): void
    {
        $concert = Concert::factory()->published()->create();

        $response = $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError($response, 'email');
    }

    #[Test]
    public function emailMustBeValidToPurchaseTickets(): void
    {
        $concert = Concert::factory()->published()->create();

        $response = $this->orderTickets($concert, [
            'email' => 'not-an-email-address',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError($response, 'email');
    }

    #[Test]
    public function ticketQuantityIsRequiredToPurchaseTickets(): void
    {
        $concert = Concert::factory()->published()->create();

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError($response, 'ticket_quantity');
    }

    #[Test]
    public function ticketQuantityMustBeAtLeastOneToPurchaseTickets(): void
    {
        $concert = Concert::factory()->published()->create();

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError($response, 'ticket_quantity');
    }

    #[Test]
    public function paymentTokenIsRequiredToPurchaseTickets(): void
    {
        $concert = Concert::factory()->published()->create();

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3
        ]);

        $this->assertValidationError($response, 'payment_token');
    }

    private function assertValidationError(TestResponse $response, string $field): void
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey($field, $response->json()['errors']);
    }

    private function orderTickets(Concert $concert, array $data): TestResponse
    {
        return $this->postJson('/concerts/' . $concert->id . '/orders', $data);
    }
}

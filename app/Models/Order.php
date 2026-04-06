<?php

namespace App\Models;

use App\Billing\Charge;
use App\ConfirmationNumberGeneratorInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Order extends Model
{

    use HasFactory;

    protected $guarded = [];

    public static function forTickets(Collection $tickets, string $email, Charge $charge): Order
    {
        $order = self::create([
            'confirmation_number' => app(ConfirmationNumberGeneratorInterface::class)->generate(),
            'email' => $email,
            'amount' => $charge->amount(),
            'card_last_four' => $charge->cardLastFour()
        ]);
        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }
        return $order;
    }

    public static function findByConfirmationNumber(string $confirmationNumber): Order
    {
        return self::where('confirmation_number', $confirmationNumber)->firstOrFail();
    }

    public function concert(): BelongsTo
    {
        return $this->belongsTo(Concert::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketQuantity(): int
    {
        return $this->tickets->count();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'confirmation_number' => $this->confirmation_number,
            'email' => $this->email,
            'ticket_quantity' => $this->tickets()->count(),
            'amount' => $this->amount
        ];
    }
}

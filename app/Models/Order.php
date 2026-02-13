<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class Order extends Model
{

    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
        $this->delete();
    }

    #[Override]
    public function toArray()
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->tickets()->count(),
            'amount' => $this->tickets()->count() * $this->concert->ticket_price
        ];
    }
}

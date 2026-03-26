<?php

namespace App;

use Override;

class OrderConfirmationNumberGenerator implements ConfirmationNumberGeneratorInterface
{

    #[Override]
    public function generate(): string
    {
        $pool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }
}

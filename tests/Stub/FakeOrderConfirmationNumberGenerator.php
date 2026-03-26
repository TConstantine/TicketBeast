<?php

namespace Tests\Stub;

use App\ConfirmationNumberGeneratorInterface;
use Override;

class FakeOrderConfirmationNumberGenerator implements ConfirmationNumberGeneratorInterface
{

    #[Override]
    public function generate(): string
    {
        return 'AHQ8VVDT58CKQDZPLQS4XW88';
    }
}

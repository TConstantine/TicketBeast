<?php

namespace App;

interface ConfirmationNumberGeneratorInterface
{

    public function generate(): string;
}

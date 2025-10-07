<?php

namespace App\Http\Exceptions;

use Exception;

class EmailAlreadyVerifiedException extends Exception
{
    public function __construct(string $message = 'Email is already verified.')
    {
        parent::__construct($message);
    }
}

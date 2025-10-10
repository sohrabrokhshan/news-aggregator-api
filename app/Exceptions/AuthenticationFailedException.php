<?php

namespace App\Exceptions;

class AuthenticationFailedException extends ClientException
{
    protected $message = 'Invalid credentials.';
    protected int $statusCode = 401;
}

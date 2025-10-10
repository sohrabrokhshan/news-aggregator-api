<?php

namespace App\Exceptions;

class FileNotFoundException extends ClientException
{
    protected $message = 'File not found';
    protected int $statusCode = 404;
}

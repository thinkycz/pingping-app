<?php

namespace App\Monitoring\Exceptions;

use RuntimeException;

class UnsafeTargetException extends RuntimeException
{
    public function __construct(public readonly string $failureCode = 'unsafe_target')
    {
        parent::__construct($failureCode);
    }
}

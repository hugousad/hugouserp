<?php

declare(strict_types=1);

namespace App\Exceptions;

class NoBranchSelectedException extends BusinessException
{
    public function __construct(string $message = 'No branch selected', int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

<?php

declare(strict_types=1);

namespace App\Exceptions;

class InsufficientStockException extends BusinessException
{
    public function __construct(
        string $productName,
        float|int $available,
        float|int $requested,
        int $code = 422,
        ?\Throwable $previous = null,
    ) {
        $message = __(
            'Insufficient stock for :product. Available: :available, requested: :requested',
            [
                'product' => $productName,
                'available' => $available,
                'requested' => $requested,
            ]
        );

        parent::__construct($message, $code, $previous);
    }
}

<?php

namespace App\DTO;

readonly class TransactionsDTO {

    public function __construct(
        public int $cartNumber,
        public float $amount,
        public string $currency,
    ) {
    }
}

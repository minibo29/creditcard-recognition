<?php

namespace App\Services;

use App\Client\BinDetectionClient;
use App\Client\ExchangeRatesApiClient;
use App\DTO\TransactionsDTO;

class CommissionTransactionService
{
    private float $europeCommission = 0.01;

    private float $defaultCommission = 0.02;

    public function __construct(
        private BinDetectionClient $binDetectionClient,
        private ExchangeRatesApiClient $exchangeRates
    ) {
    }

    public function getTransactionCommission(TransactionsDTO $transactionsDTO): float
    {
        $isEuropean = $this->binDetectionClient->isCardEuropean($transactionsDTO->cartNumber);

        $amount = $transactionsDTO->amount;

        if ($transactionsDTO->currency !== 'EUR') {
            $amount = $this->exchangeRates->convertToEuro($transactionsDTO->amount, $transactionsDTO->currency);
        }

        return round($this->calculateCommission($amount, $isEuropean), 2, PHP_ROUND_HALF_UP) ;
    }

    public function calculateCommission(float $amount, bool $isEuropean): float
    {
        return $amount * ($isEuropean ? $this->europeCommission : $this->defaultCommission);
    }
}

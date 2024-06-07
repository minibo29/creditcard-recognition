<?php

namespace App\Client;

interface ExchangeRatesClientInterface
{
    public function convertTo(float $amount, string $from, string $to): float;
    public function convertToEuro(float $amount, string $from): float;
}

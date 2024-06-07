<?php

namespace App\Tests;

use App\Client\BinDetectionClient;
use App\Client\ExchangeRatesApiClient;
use App\DTO\TransactionsMapper;
use App\ExchangeRates\ExchangeRates;
use App\Services\CommissionTransactionService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


class AppTest extends TestCase
{
    private CommissionTransactionService $commissionService;

    public function setUp(): void
    {
        $binDetectionClient = $this->createStub(BinDetectionClient::class);
        $binDetectionClient->method('isCardEuropean')
            ->willReturnCallback(function ($cardNumber) {
                return match ($cardNumber) {
                    '45717360', '516793', '4745030' => true,
                    '45417360' => false,
                    default => throw new \RuntimeException(''),
                };
            })
        ;

        $exchangeRates = $this->createStub(ExchangeRatesApiClient::class);
        $exchangeRates->method('getExchangeBy')
            ->willReturn([
                "success" => true,
                "timestamp" => 1519296206,
                "base" => "EUR",
                "date" => "2021-03-17",
                "rates" => [
                    "AUD" => 1.566015,
                    "CAD" => 1.560132,
                    "CHF" => 1.154727,
                    "CNY" => 7.827874,
                    "GBP" => 0.882047,
                    "JPY" => 132.360679,
                    "USD" => 1.23396,
                ]
            ])
        ;

        $exchangeRates->method('convertToEuro')
            ->willReturnCallback(function ($amount, $from) {
                return match ($from) {
                    "EUR" => $amount / 1,
                    "AUD" => $amount / 1.566015,
                    "CAD" => $amount / 1.560132,
                    "CHF" => $amount / 1.154727,
                    "CNY" => $amount / 7.827874,
                    "GBP" => $amount / 0.882047,
                    "JPY" => $amount / 132.360679,
                    "USD" => $amount / 1.23396,
                };
            })
        ;

        $this->commissionService = new CommissionTransactionService($binDetectionClient, $exchangeRates);
    }
    
    #[DataProvider('additionProvider')]
    #[Test]
    public function check_transaction_commission(float $expected, string $inputText): void
    {
        // Set Up
        $dto = (new TransactionsMapper())->mapDto($inputText);
        // Do something
        $transactionCommission = $this->commissionService->getTransactionCommission($dto);
        // Make assertions
        $this->assertSame($transactionCommission, $expected);
    }

    // return multiple sets of operands and results
    public static function additionProvider(): array
    {
        return [
            [1, '{"bin":"45717360","amount":"100.00","currency":"EUR"}',],
            [0.41, '{"bin":"516793","amount":"50.00","currency":"USD"}',],
            [1.51, '{"bin":"45417360","amount":"10000.00","currency":"JPY"}',],
            [22.67, '{"bin":"4745030","amount":"2000.00","currency":"GBP"}',],
        ];
    }

}

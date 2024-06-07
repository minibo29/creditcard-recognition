<?php

namespace App\Client;

class ExchangeRatesApiClient implements ExchangeRatesClientInterface
{
    private string $url = 'https://api.exchangeratesapi.io/';
    private string $accessKey = '';
    
    private ClientInterface $client;
    private array $cache = [];

    public function __construct(string $accessKey = '')
    {
        if (empty($accessKey)) {
            // mock
            $accessKey = '03bf87173c643e1928fdac427e2f3fd3';
        }

        $this->accessKey = $accessKey;
        $this->client = new GuzzleClientAdapter();
    }

    public function convertTo(float $amount, string $from, string $to): float
    {
        $exchangeResponse = $this->getExchangeBy($to);

        if (empty($exchangeResponse['rates'][$from]))
            throw new \RuntimeException('Something wrong with "Exchange Rates Api"');

        return $amount / $exchangeResponse['rates'][$from];
    }

    public function convertToEuro(float $amount, string $from): float
    {
        return $this->convertTo($amount, $from, 'EUR');
    }

    public function getExchangeBy(string $to): array
    {
        if (isset($this->cache[$to])) {
            return $this->cache[$to];
        }

        $urlParams = [
            'access_key' => $this->accessKey,
            'base' => $to
        ];

        $response = $this->client->get($this->url . 'latest' . '?' . http_build_query($urlParams));

        if ($response->getStatusCode() !== 200 || json_validate($response->getBody())) {
            throw new \RuntimeException('Something wrong with "Exchange Rates Api"');
        }

        $this->cache[$to] = json_decode($response->getBody(), true);

        return $this->cache[$to];
    }
}

<?php

namespace App\Client;

use App\Interface\BinDetectionInterface;

class BinDetectionClient implements BinDetectionInterface
{
    private string $url = 'https://lookup.binlist.net/';
    private ClientInterface $client;

    public function __construct()
    {
        $this->client = new GuzzleClientAdapter();
    }

    public function detectCard(string $cardNumber): array|false
    {
        try {
            $response = $this->client->get($this->url . $cardNumber);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Something wrong with "BinDetectionClient"');
        }

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Something wrong with "BinDetectionClient"');
        }

        $body = $response->getBody();
        if (!json_validate($body)) {
            throw new \RuntimeException('Something wrong with "BinDetectionClient"');
        }

        return json_decode($body, true);
    }

    public function isCardEuropean(string $cardNumber): bool
    {
        $dedicatedResponse = $this->detectCard($cardNumber);
        if (empty($dedicatedResponse['country']['alpha2'])) {
            throw new \RuntimeException('Something wrong with "BinDetectionClient"');
        }

        return in_array($dedicatedResponse['country']['alpha2'], BinDetectionInterface::EUROPE_COUNTRIES);
    }
}

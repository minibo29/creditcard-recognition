<?php

namespace App\Client;

use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class GuzzleClientAdapter implements ClientInterface
{
    private Client $client;
    public function __construct()
    {
        $this->client = new Client();
    }

    public function __call(string $method, array $args): mixed
    {
        return $this->client->$method($args);
    }

    #[\Override]
    public function request(
        string $method,
        UriInterface|string $uri,
        array $options = []
    ): ResponseInterface {
        return $this->client->request($method, $uri, $options);
    }

    #[\Override]
    public function get(UriInterface|string $uri, array $options = []): ResponseInterface
    {
        return $this->client->get($uri, $options);
    }

    #[\Override]
    public function post(UriInterface|string $uri, array $options = []): ResponseInterface
    {
        return $this->client->post($uri, $options);
    }

    #[\Override]
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}

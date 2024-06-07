<?php

namespace App\Client;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ClientInterface extends \Psr\Http\Client\ClientInterface
{
    public function request(string $method, string|UriInterface $uri, array $options = []): ResponseInterface;
    public function get(string|UriInterface $uri, array $options = []): ResponseInterface;
    public function post(string|UriInterface $uri, array $options = []): ResponseInterface;
}

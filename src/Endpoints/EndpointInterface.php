<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

interface EndpointInterface
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function call(SowisoApiContext $context, SowisoApiPayload $payload, array $data): array;

    public function withConfiguration(SowisoApiConfiguration $configuration): self;

    public function withHttpClient(ClientInterface $httpClient): self;

    public function withHttpRequestFactory(RequestFactoryInterface $httpRequestFactory): self;

    public function withHttpStreamFactory(StreamFactoryInterface $httpStreamFactory): self;

    /**
     * @param array<CallbackInterface<RequestInterface, ResponseInterface>> $callbacks
     */
    public function withCallbacks(array $callbacks): self;

    /**
     * @param RequestHandlerInterface<EndpointInterface, RequestInterface, ResponseInterface>|null $requestHandler
     */
    public function withRequestHandler(?RequestHandlerInterface $requestHandler): self;
}

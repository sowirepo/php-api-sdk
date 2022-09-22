<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\SowisoApiContext;

interface EndpointInterface
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function call(SowisoApiContext $context, array $data): array;

    public function withConfiguration(SowisoApiConfiguration $configuration): self;

    public function withHttpClient(ClientInterface $httpClient): self;

    public function withHttpRequestFactory(RequestFactoryInterface $httpRequestFactory): self;

    public function withHttpStreamFactory(StreamFactoryInterface $httpStreamFactory): self;

    /**
     * @param array<CallbackInterface> $callbacks
     */
    public function withCallbacks(array $callbacks): self;
}

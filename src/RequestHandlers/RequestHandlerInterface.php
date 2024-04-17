<?php

declare(strict_types=1);

namespace Sowiso\SDK\RequestHandlers;

use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @template TEndpoint of EndpointInterface
 * @template TRequest of RequestInterface
 * @template TResponse of ResponseInterface
 */
interface RequestHandlerInterface
{
    /**
     * @return class-string<TEndpoint>
     */
    public function endpoint(): string;

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param TEndpoint $endpoint
     * @param TRequest $request
     * @return TResponse|null
     */
    public function handleRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        EndpointInterface $endpoint,
        RequestInterface $request,
    ): ?ResponseInterface;
}

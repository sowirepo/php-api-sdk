<?php

declare(strict_types=1);

namespace Sowiso\SDK\Callbacks;

use Exception;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @template TRequest of RequestInterface
 * @template TResponse of ResponseInterface
 */
interface CallbackInterface
{
    /**
     * @return class-string<EndpointInterface>
     */
    public function endpoint(): string;

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int;

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param TRequest $request
     * @return void
     */
    public function request(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request): void;

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param TResponse $response
     * @return void
     */
    public function response(SowisoApiContext $context, SowisoApiPayload $payload, ResponseInterface $response): void;

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param TRequest $request
     * @param TResponse $response
     * @return void
     */
    public function success(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request, ResponseInterface $response): void;

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param Exception $exception
     * @return void
     */
    public function failure(SowisoApiContext $context, SowisoApiPayload $payload, Exception $exception): void;
}

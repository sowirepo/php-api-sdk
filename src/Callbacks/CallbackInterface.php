<?php

declare(strict_types=1);

namespace Sowiso\SDK\Callbacks;

use Exception;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

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
     * @param TRequest $request
     * @return void
     */
    public function request(SowisoApiContext $context, RequestInterface $request): void;

    /**
     * @param SowisoApiContext $context
     * @param TResponse $response
     * @return void
     */
    public function response(SowisoApiContext $context, ResponseInterface $response): void;

    /**
     * @param SowisoApiContext $context
     * @param TRequest $request
     * @param TResponse $response
     * @return void
     */
    public function success(SowisoApiContext $context, RequestInterface $request, ResponseInterface $response): void;

    /**
     * @param SowisoApiContext $context
     * @param Exception $exception
     * @return void
     */
    public function failure(SowisoApiContext $context, Exception $exception): void;
}

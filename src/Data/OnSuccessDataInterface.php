<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @template TRequest of RequestInterface
 * @template TResponse of ResponseInterface
 */
interface OnSuccessDataInterface
{
    /**
     * @return SowisoApiContext
     */
    public function getContext(): SowisoApiContext;

    /**
     * @return TRequest
     */
    public function getRequest(): RequestInterface;

    /**
     * @return TResponse
     */
    public function getResponse(): ResponseInterface;
}

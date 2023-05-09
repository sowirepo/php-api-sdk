<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @template TRequest of RequestInterface
 */
interface OnRequestDataInterface
{
    /**
     * @return SowisoApiContext
     */
    public function getContext(): SowisoApiContext;

    /**
     * @return SowisoApiPayload
     */
    public function getPayload(): SowisoApiPayload;

    /**
     * @return TRequest
     */
    public function getRequest(): RequestInterface;
}

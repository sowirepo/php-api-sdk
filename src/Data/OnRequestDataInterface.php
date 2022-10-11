<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\SowisoApiContext;

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
     * @return TRequest
     */
    public function getRequest(): RequestInterface;
}

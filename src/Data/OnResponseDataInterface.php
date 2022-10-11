<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @template TResponse of ResponseInterface
 */
interface OnResponseDataInterface
{
    /**
     * @return SowisoApiContext
     */
    public function getContext(): SowisoApiContext;

    /**
     * @return TResponse
     */
    public function getResponse(): ResponseInterface;
}

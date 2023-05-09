<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

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
     * @return SowisoApiPayload
     */
    public function getPayload(): SowisoApiPayload;

    /**
     * @return TResponse
     */
    public function getResponse(): ResponseInterface;
}

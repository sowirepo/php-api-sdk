<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Exception;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

interface OnFailureDataInterface
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
     * @return Exception
     */
    public function getException(): Exception;
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Exception;
use Sowiso\SDK\SowisoApiContext;

interface OnFailureDataInterface
{
    /**
     * @return SowisoApiContext
     */
    public function getContext(): SowisoApiContext;

    /**
     * @return Exception
     */
    public function getException(): Exception;
}

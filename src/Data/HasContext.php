<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Sowiso\SDK\SowisoApiContext;

trait HasContext
{
    protected SowisoApiContext $context;

    public function getContext(): SowisoApiContext
    {
        return $this->context;
    }
}

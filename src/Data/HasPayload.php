<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

use Sowiso\SDK\SowisoApiPayload;

trait HasPayload
{
    protected SowisoApiPayload $payload;

    public function getPayload(): SowisoApiPayload
    {
        return $this->payload;
    }
}

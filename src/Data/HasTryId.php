<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

trait HasTryId
{
    protected int $tryId;

    public function getTryId(): int
    {
        return $this->tryId;
    }
}

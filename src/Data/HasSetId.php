<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

trait HasSetId
{
    protected int $setId;

    public function getSetId(): int
    {
        return $this->setId;
    }
}

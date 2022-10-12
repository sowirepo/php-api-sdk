<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data;

trait HasExerciseId
{
    protected int $exerciseId;

    public function getExerciseId(): int
    {
        return $this->exerciseId;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\ScoreCapture\Data;

use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasTryId;
use Sowiso\SDK\SowisoApiContext;

class OnScoreData
{
    use HasContext;
    use HasTryId;

    public function __construct(
        protected SowisoApiContext $context,
        protected string $source,
        protected int $tryId,
        protected bool $completed,
        protected float $score,
    ) {
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function getScore(): float
    {
        return $this->score;
    }
}

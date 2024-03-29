<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\ScoreCapture\Data;

use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\HasTryId;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class OnScoreData
{
    use HasContext;
    use HasPayload;
    use HasTryId;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected string $source,
        protected int $tryId,
        protected bool $completed,
        protected bool $setCompleted,
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

    public function isSetCompleted(): bool
    {
        return $this->setCompleted;
    }

    public function getScore(): float
    {
        return $this->score;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\DataCapture\Data;

use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\HasSetId;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class OnRegisterExerciseSetData
{
    use HasContext;
    use HasPayload;
    use HasSetId;

    /**
     * @param array<int, array{exerciseId: int, tryId: int}> $exerciseTries
     */
    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected int $setId,
        protected array $exerciseTries,
    ) {
    }

    /**
     * @return array<int, array{exerciseId: int, tryId: int}>
     */
    public function getExerciseTries(): array
    {
        return $this->exerciseTries;
    }
}

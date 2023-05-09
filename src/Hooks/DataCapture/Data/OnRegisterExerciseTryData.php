<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\DataCapture\Data;

use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasExerciseId;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\HasSetId;
use Sowiso\SDK\Data\HasTryId;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class OnRegisterExerciseTryData
{
    use HasContext;
    use HasPayload;
    use HasSetId;
    use HasExerciseId;
    use HasTryId;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected int $setId,
        protected int $exerciseId,
        protected int $tryId,
    ) {
    }
}

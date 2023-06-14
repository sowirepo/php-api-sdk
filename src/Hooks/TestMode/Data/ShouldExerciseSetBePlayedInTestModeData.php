<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\TestMode\Data;

use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\HasSetId;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class ShouldExerciseSetBePlayedInTestModeData
{
    use HasContext;
    use HasPayload;
    use HasSetId;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected int $setId,
    ) {
    }
}

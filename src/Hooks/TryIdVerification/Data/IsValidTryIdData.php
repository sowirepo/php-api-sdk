<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\TryIdVerification\Data;

use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\HasTryId;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class IsValidTryIdData
{
    use HasContext;
    use HasPayload;
    use HasTryId;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected int $tryId,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\TryIdVerification\Data;

use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasTryId;
use Sowiso\SDK\SowisoApiContext;

class IsValidTryIdData
{
    use HasContext;
    use HasTryId;

    public function __construct(
        protected SowisoApiContext $context,
        protected int $tryId,
    ) {
    }
}

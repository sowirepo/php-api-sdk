<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution\Data;

use Exception;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnFailureDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlaySolutionOnFailureData implements OnFailureDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected Exception $exception,
    ) {
    }

    public function getException(): Exception
    {
        return $this->exception;
    }
}

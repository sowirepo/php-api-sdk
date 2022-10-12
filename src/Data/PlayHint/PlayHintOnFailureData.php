<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayHint;

use Exception;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnFailureDataInterface;
use Sowiso\SDK\SowisoApiContext;

class PlayHintOnFailureData implements OnFailureDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected Exception $exception,
    ) {
    }

    public function getException(): Exception
    {
        return $this->exception;
    }
}

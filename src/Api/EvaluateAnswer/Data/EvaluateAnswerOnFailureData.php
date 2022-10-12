<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer\Data;

use Exception;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnFailureDataInterface;
use Sowiso\SDK\SowisoApiContext;

class EvaluateAnswerOnFailureData implements OnFailureDataInterface
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

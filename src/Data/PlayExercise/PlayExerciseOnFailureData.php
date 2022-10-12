<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayExercise;

use Exception;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnFailureDataInterface;
use Sowiso\SDK\SowisoApiContext;

class PlayExerciseOnFailureData implements OnFailureDataInterface
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

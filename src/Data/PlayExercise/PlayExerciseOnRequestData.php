<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayExercise;

use Sowiso\SDK\Api\PlayExercise\PlayExerciseRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnRequestDataInterface<PlayExerciseRequest>
 */
class PlayExerciseOnRequestData implements OnRequestDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlayExerciseRequest $request,
    ) {
    }

    public function getRequest(): PlayExerciseRequest
    {
        return $this->request;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayExerciseSet;

use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnRequestDataInterface<PlayExerciseSetRequest>
 */
class PlayExerciseSetOnRequestData implements OnRequestDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlayExerciseSetRequest $request,
    ) {
    }

    public function getRequest(): PlayExerciseSetRequest
    {
        return $this->request;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayExerciseSet;

use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnSuccessDataInterface<PlayExerciseSetRequest, PlayExerciseSetResponse>
 */
class PlayExerciseSetOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlayExerciseSetRequest $request,
        protected PlayExerciseSetResponse $response,
    ) {
    }

    public function getRequest(): PlayExerciseSetRequest
    {
        return $this->request;
    }

    public function getResponse(): PlayExerciseSetResponse
    {
        return $this->response;
    }
}

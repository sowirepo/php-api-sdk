<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet\Data;

use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnSuccessDataInterface<PlayExerciseSetRequest, PlayExerciseSetResponse>
 */
class PlayExerciseSetOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
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

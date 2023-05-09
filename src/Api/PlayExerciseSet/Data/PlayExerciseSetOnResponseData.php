<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet\Data;

use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnResponseDataInterface<PlayExerciseSetResponse>
 */
class PlayExerciseSetOnResponseData implements OnResponseDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlayExerciseSetResponse $response,
    ) {
    }

    public function getResponse(): PlayExerciseSetResponse
    {
        return $this->response;
    }
}

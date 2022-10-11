<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayExerciseSet;

use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnResponseDataInterface<PlayExerciseSetResponse>
 */
class PlayExerciseSetOnResponseData implements OnResponseDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlayExerciseSetResponse $response,
    ) {
    }

    public function getResponse(): PlayExerciseSetResponse
    {
        return $this->response;
    }
}

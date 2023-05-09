<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise\Data;

use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnRequestDataInterface<PlayExerciseRequest>
 */
class PlayExerciseOnRequestData implements OnRequestDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlayExerciseRequest $request,
    ) {
    }

    public function getRequest(): PlayExerciseRequest
    {
        return $this->request;
    }
}

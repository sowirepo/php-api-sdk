<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise\Data;

use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseRequest;
use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnSuccessDataInterface<PlayExerciseRequest, PlayExerciseResponse>
 */
class PlayExerciseOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlayExerciseRequest $request,
        protected PlayExerciseResponse $response,
    ) {
    }

    public function getRequest(): PlayExerciseRequest
    {
        return $this->request;
    }

    public function getResponse(): PlayExerciseResponse
    {
        return $this->response;
    }
}

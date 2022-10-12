<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayExercise;

use Sowiso\SDK\Api\PlayExercise\PlayExerciseRequest;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnSuccessDataInterface<PlayExerciseRequest, PlayExerciseResponse>
 */
class PlayExerciseOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
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

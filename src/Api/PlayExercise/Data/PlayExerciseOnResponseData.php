<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise\Data;

use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnResponseDataInterface<PlayExerciseResponse>
 */
class PlayExerciseOnResponseData implements OnResponseDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlayExerciseResponse $response,
    ) {
    }

    public function getResponse(): PlayExerciseResponse
    {
        return $this->response;
    }
}

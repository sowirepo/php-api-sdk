<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

class PlayExerciseSetEndpoint extends AbstractEndpoint
{
    public const NAME = "play/set";

    protected function createRequest(
        SowisoApiContext $context,
        array $data,
    ): RequestInterface {
        return new PlayExerciseSetRequest($context, $data);
    }

    protected function createResponse(
        SowisoApiContext $context,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new PlayExerciseSetResponse($context, $data, $request);
    }
}

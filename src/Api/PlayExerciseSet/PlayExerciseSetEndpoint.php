<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlayExerciseSetEndpoint extends AbstractEndpoint
{
    public const NAME = "play/set";

    public function createRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
    ): RequestInterface {
        return new PlayExerciseSetRequest($context, $payload, $data);
    }

    public function createResponse(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new PlayExerciseSetResponse($context, $payload, $data, $request);
    }
}

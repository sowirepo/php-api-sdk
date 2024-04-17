<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise;

use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseRequest;
use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlayExerciseEndpoint extends AbstractEndpoint
{
    public const NAME = "play/exercise";

    public function createRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
    ): RequestInterface {
        return new PlayExerciseRequest($context, $payload, $data);
    }

    public function createResponse(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new PlayExerciseResponse($context, $payload, $data, $request);
    }
}

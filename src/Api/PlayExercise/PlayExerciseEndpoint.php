<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise;

use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

class PlayExerciseEndpoint extends AbstractEndpoint
{
    public const NAME = "play/exercise";

    protected function createRequest(
        SowisoApiContext $context,
        array $data,
    ): RequestInterface {
        return new PlayExerciseRequest($context, $data);
    }

    protected function createResponse(
        SowisoApiContext $context,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new PlayExerciseResponse($context, $data, $request);
    }
}

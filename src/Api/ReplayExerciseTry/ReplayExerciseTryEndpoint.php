<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\ReplayExerciseTry;

use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryRequest;
use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class ReplayExerciseTryEndpoint extends AbstractEndpoint
{
    public const NAME = "replay/try";

    protected function createRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
    ): RequestInterface {
        return new ReplayExerciseTryRequest($context, $payload, $data);
    }

    protected function createResponse(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new ReplayExerciseTryResponse($context, $payload, $data, $request);
    }
}

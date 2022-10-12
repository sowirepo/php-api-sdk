<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution;

use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionRequest;
use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

class PlaySolutionEndpoint extends AbstractEndpoint
{
    public const NAME = "play/solution";

    protected function createRequest(
        SowisoApiContext $context,
        array $data,
    ): RequestInterface {
        return new PlaySolutionRequest($context, $data);
    }

    protected function createResponse(
        SowisoApiContext $context,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new PlaySolutionResponse($context, $data, $request);
    }
}

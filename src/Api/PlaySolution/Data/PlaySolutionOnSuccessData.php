<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution\Data;

use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionRequest;
use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnSuccessDataInterface<PlaySolutionRequest, PlaySolutionResponse>
 */
class PlaySolutionOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlaySolutionRequest $request,
        protected PlaySolutionResponse $response,
    ) {
    }

    public function getRequest(): PlaySolutionRequest
    {
        return $this->request;
    }

    public function getResponse(): PlaySolutionResponse
    {
        return $this->response;
    }
}

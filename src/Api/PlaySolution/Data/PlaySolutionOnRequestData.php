<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution\Data;

use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnRequestDataInterface<PlaySolutionRequest>
 */
class PlaySolutionOnRequestData implements OnRequestDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlaySolutionRequest $request,
    ) {
    }

    public function getRequest(): PlaySolutionRequest
    {
        return $this->request;
    }
}

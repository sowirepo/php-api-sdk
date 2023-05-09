<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution\Data;

use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnRequestDataInterface<PlaySolutionRequest>
 */
class PlaySolutionOnRequestData implements OnRequestDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlaySolutionRequest $request,
    ) {
    }

    public function getRequest(): PlaySolutionRequest
    {
        return $this->request;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution\Data;

use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnResponseDataInterface<PlaySolutionResponse>
 */
class PlaySolutionOnResponseData implements OnResponseDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlaySolutionResponse $response,
    ) {
    }

    public function getResponse(): PlaySolutionResponse
    {
        return $this->response;
    }
}

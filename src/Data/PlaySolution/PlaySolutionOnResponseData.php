<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlaySolution;

use Sowiso\SDK\Api\PlaySolution\PlaySolutionResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnResponseDataInterface<PlaySolutionResponse>
 */
class PlaySolutionOnResponseData implements OnResponseDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlaySolutionResponse $response,
    ) {
    }

    public function getResponse(): PlaySolutionResponse
    {
        return $this->response;
    }
}

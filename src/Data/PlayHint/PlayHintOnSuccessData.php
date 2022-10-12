<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayHint;

use Sowiso\SDK\Api\PlayHint\PlayHintRequest;
use Sowiso\SDK\Api\PlayHint\PlayHintResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnSuccessDataInterface<PlayHintRequest, PlayHintResponse>
 */
class PlayHintOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlayHintRequest $request,
        protected PlayHintResponse $response,
    ) {
    }

    public function getRequest(): PlayHintRequest
    {
        return $this->request;
    }

    public function getResponse(): PlayHintResponse
    {
        return $this->response;
    }
}
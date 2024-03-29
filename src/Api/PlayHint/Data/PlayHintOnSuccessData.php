<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayHint\Data;

use Sowiso\SDK\Api\PlayHint\Http\PlayHintRequest;
use Sowiso\SDK\Api\PlayHint\Http\PlayHintResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnSuccessDataInterface<PlayHintRequest, PlayHintResponse>
 */
class PlayHintOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
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

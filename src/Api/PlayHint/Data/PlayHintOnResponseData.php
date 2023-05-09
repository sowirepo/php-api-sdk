<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayHint\Data;

use Sowiso\SDK\Api\PlayHint\Http\PlayHintResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnResponseDataInterface<PlayHintResponse>
 */
class PlayHintOnResponseData implements OnResponseDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlayHintResponse $response,
    ) {
    }

    public function getResponse(): PlayHintResponse
    {
        return $this->response;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayHint\Data;

use Sowiso\SDK\Api\PlayHint\Http\PlayHintRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnRequestDataInterface<PlayHintRequest>
 */
class PlayHintOnRequestData implements OnRequestDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlayHintRequest $request,
    ) {
    }

    public function getRequest(): PlayHintRequest
    {
        return $this->request;
    }
}

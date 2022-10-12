<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\PlayHint;

use Sowiso\SDK\Api\PlayHint\PlayHintRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnRequestDataInterface<PlayHintRequest>
 */
class PlayHintOnRequestData implements OnRequestDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected PlayHintRequest $request,
    ) {
    }

    public function getRequest(): PlayHintRequest
    {
        return $this->request;
    }
}

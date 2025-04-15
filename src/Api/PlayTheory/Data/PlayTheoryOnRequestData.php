<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayTheory\Data;

use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnRequestDataInterface<PlayTheoryRequest>
 */
class PlayTheoryOnRequestData implements OnRequestDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlayTheoryRequest $request,
    ) {
    }

    public function getRequest(): PlayTheoryRequest
    {
        return $this->request;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\ReplayExerciseTry\Data;

use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnRequestDataInterface<ReplayExerciseTryRequest>
 */
class ReplayExerciseTryOnRequestData implements OnRequestDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected ReplayExerciseTryRequest $request,
    ) {
    }

    public function getRequest(): ReplayExerciseTryRequest
    {
        return $this->request;
    }
}

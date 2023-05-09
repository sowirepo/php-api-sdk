<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\ReplayExerciseTry\Data;

use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnResponseDataInterface<ReplayExerciseTryResponse>
 */
class ReplayExerciseTryOnResponseData implements OnResponseDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected ReplayExerciseTryResponse $response,
    ) {
    }

    public function getResponse(): ReplayExerciseTryResponse
    {
        return $this->response;
    }
}

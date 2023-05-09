<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\ReplayExerciseTry\Data;

use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryRequest;
use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnSuccessDataInterface<ReplayExerciseTryRequest, ReplayExerciseTryResponse>
 */
class ReplayExerciseTryOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected ReplayExerciseTryRequest $request,
        protected ReplayExerciseTryResponse $response,
    ) {
    }

    public function getRequest(): ReplayExerciseTryRequest
    {
        return $this->request;
    }

    public function getResponse(): ReplayExerciseTryResponse
    {
        return $this->response;
    }
}

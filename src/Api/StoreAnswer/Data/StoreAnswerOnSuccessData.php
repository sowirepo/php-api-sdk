<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer\Data;

use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerRequest;
use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnSuccessDataInterface<StoreAnswerRequest, StoreAnswerResponse>
 */
class StoreAnswerOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected StoreAnswerRequest $request,
        protected StoreAnswerResponse $response,
    ) {
    }

    public function getRequest(): StoreAnswerRequest
    {
        return $this->request;
    }

    public function getResponse(): StoreAnswerResponse
    {
        return $this->response;
    }
}

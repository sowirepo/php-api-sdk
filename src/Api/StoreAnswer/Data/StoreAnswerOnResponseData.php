<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer\Data;

use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnResponseDataInterface<StoreAnswerResponse>
 */
class StoreAnswerOnResponseData implements OnResponseDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected StoreAnswerResponse $response,
    ) {
    }

    public function getResponse(): StoreAnswerResponse
    {
        return $this->response;
    }
}

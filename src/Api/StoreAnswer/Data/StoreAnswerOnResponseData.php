<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer\Data;

use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnResponseDataInterface<StoreAnswerResponse>
 */
class StoreAnswerOnResponseData implements OnResponseDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected StoreAnswerResponse $response,
    ) {
    }

    public function getResponse(): StoreAnswerResponse
    {
        return $this->response;
    }
}

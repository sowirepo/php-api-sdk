<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer\Data;

use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnRequestDataInterface<StoreAnswerRequest>
 */
class StoreAnswerOnRequestData implements OnRequestDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected StoreAnswerRequest $request,
    ) {
    }

    public function getRequest(): StoreAnswerRequest
    {
        return $this->request;
    }
}

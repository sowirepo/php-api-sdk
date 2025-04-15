<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayTheory\Data;

use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryRequest;
use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnSuccessDataInterface<PlayTheoryRequest, PlayTheoryResponse>
 */
class PlayTheoryOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlayTheoryRequest $request,
        protected PlayTheoryResponse $response,
    ) {
    }

    public function getRequest(): PlayTheoryRequest
    {
        return $this->request;
    }

    public function getResponse(): PlayTheoryResponse
    {
        return $this->response;
    }
}

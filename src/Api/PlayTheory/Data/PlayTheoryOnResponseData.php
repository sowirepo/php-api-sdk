<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayTheory\Data;

use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnResponseDataInterface<PlayTheoryResponse>
 */
class PlayTheoryOnResponseData implements OnResponseDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected PlayTheoryResponse $response,
    ) {
    }

    public function getResponse(): PlayTheoryResponse
    {
        return $this->response;
    }
}

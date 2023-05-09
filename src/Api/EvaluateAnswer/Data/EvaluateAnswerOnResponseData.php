<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer\Data;

use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\HasPayload;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements OnResponseDataInterface<EvaluateAnswerResponse>
 */
class EvaluateAnswerOnResponseData implements OnResponseDataInterface
{
    use HasContext;
    use HasPayload;

    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected EvaluateAnswerResponse $response,
    ) {
    }

    public function getResponse(): EvaluateAnswerResponse
    {
        return $this->response;
    }
}

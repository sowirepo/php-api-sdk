<?php

declare(strict_types=1);

namespace Sowiso\SDK\Data\EvaluateAnswer;

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnResponseDataInterface<EvaluateAnswerResponse>
 */
class EvaluateAnswerOnResponseData implements OnResponseDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected EvaluateAnswerResponse $response,
    ) {
    }

    public function getResponse(): EvaluateAnswerResponse
    {
        return $this->response;
    }
}

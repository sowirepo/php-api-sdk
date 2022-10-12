<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer\Data;

use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerRequest;
use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerResponse;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnSuccessDataInterface<EvaluateAnswerRequest, EvaluateAnswerResponse>
 */
class EvaluateAnswerOnSuccessData implements OnSuccessDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected EvaluateAnswerRequest $request,
        protected EvaluateAnswerResponse $response,
    ) {
    }

    public function getRequest(): EvaluateAnswerRequest
    {
        return $this->request;
    }

    public function getResponse(): EvaluateAnswerResponse
    {
        return $this->response;
    }
}

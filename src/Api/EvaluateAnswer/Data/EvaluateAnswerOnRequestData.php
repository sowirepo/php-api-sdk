<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer\Data;

use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerRequest;
use Sowiso\SDK\Data\HasContext;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements OnRequestDataInterface<EvaluateAnswerRequest>
 */
class EvaluateAnswerOnRequestData implements OnRequestDataInterface
{
    use HasContext;

    public function __construct(
        protected SowisoApiContext $context,
        protected EvaluateAnswerRequest $request,
    ) {
    }

    public function getRequest(): EvaluateAnswerRequest
    {
        return $this->request;
    }
}

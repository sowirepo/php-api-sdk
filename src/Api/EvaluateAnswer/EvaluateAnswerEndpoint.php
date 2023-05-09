<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerRequest;
use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class EvaluateAnswerEndpoint extends AbstractEndpoint
{
    public const NAME = "evaluate/answer";

    protected function createRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
    ): RequestInterface {
        return new EvaluateAnswerRequest($context, $payload, $data);
    }

    protected function createResponse(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new EvaluateAnswerResponse($context, $payload, $data, $request);
    }
}

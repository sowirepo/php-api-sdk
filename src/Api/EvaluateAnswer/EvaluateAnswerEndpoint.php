<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerRequest;
use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class EvaluateAnswerEndpoint extends AbstractEndpoint
{
    public const NAME = "evaluate/answer";

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    protected function createRequest(
        SowisoApiContext $context,
        array $data,
    ): RequestInterface {
        return new EvaluateAnswerRequest($context, $data);
    }

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    protected function createResponse(
        SowisoApiContext $context,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new EvaluateAnswerResponse($context, $data, $request);
    }
}

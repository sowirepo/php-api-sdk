<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Exception;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Data\EvaluateAnswer\EvaluateAnswerOnFailureData;
use Sowiso\SDK\Data\EvaluateAnswer\EvaluateAnswerOnRequestData;
use Sowiso\SDK\Data\EvaluateAnswer\EvaluateAnswerOnResponseData;
use Sowiso\SDK\Data\EvaluateAnswer\EvaluateAnswerOnSuccessData;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements CallbackInterface<EvaluateAnswerRequest, EvaluateAnswerResponse>
 */
class EvaluateAnswerCallback implements CallbackInterface
{
    /**
     * @param EvaluateAnswerOnRequestData $data
     * @return void
     */
    public function onRequest(EvaluateAnswerOnRequestData $data): void
    {
    }

    /**
     * @param EvaluateAnswerOnResponseData $data
     * @return void
     */
    public function onResponse(EvaluateAnswerOnResponseData $data): void
    {
    }

    /**
     * @param EvaluateAnswerOnSuccessData $data
     * @return void
     */
    public function onSuccess(EvaluateAnswerOnSuccessData $data): void
    {
    }

    /**
     * @param EvaluateAnswerOnFailureData $data
     * @return void
     */
    public function onFailure(EvaluateAnswerOnFailureData $data): void
    {
    }

    /**
     * @return class-string<EvaluateAnswerEndpoint>
     */
    final public function endpoint(): string
    {
        return EvaluateAnswerEndpoint::class;
    }

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int
    {
        return CallbackPriority::MEDIUM;
    }

    /**
     * @param EvaluateAnswerRequest $request
     */
    final public function request(SowisoApiContext $context, RequestInterface $request): void
    {
        $this->onRequest(new EvaluateAnswerOnRequestData($context, $request));
    }

    /**
     * @param EvaluateAnswerResponse $response
     */
    final public function response(SowisoApiContext $context, ResponseInterface $response): void
    {
        $this->onResponse(new EvaluateAnswerOnResponseData($context, $response));
    }

    /**
     * @param EvaluateAnswerRequest $request
     * @param EvaluateAnswerResponse $response
     */
    final public function success(SowisoApiContext $context, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new EvaluateAnswerOnSuccessData($context, $request, $response));
    }

    final public function failure(SowisoApiContext $context, Exception $exception): void
    {
        $this->onFailure(new EvaluateAnswerOnFailureData($context, $exception));
    }
}

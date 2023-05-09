<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Exception;
use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnFailureData;
use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnRequestData;
use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnResponseData;
use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnSuccessData;
use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerRequest;
use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

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
    final public function request(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request): void
    {
        $this->onRequest(new EvaluateAnswerOnRequestData($context, $payload, $request));
    }

    /**
     * @param EvaluateAnswerResponse $response
     */
    final public function response(SowisoApiContext $context, SowisoApiPayload $payload, ResponseInterface $response): void
    {
        $this->onResponse(new EvaluateAnswerOnResponseData($context, $payload, $response));
    }

    /**
     * @param EvaluateAnswerRequest $request
     * @param EvaluateAnswerResponse $response
     */
    final public function success(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new EvaluateAnswerOnSuccessData($context, $payload, $request, $response));
    }

    final public function failure(SowisoApiContext $context, SowisoApiPayload $payload, Exception $exception): void
    {
        $this->onFailure(new EvaluateAnswerOnFailureData($context, $payload, $exception));
    }
}

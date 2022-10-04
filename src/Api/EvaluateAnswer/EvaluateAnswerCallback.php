<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Exception;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements CallbackInterface<EvaluateAnswerRequest, EvaluateAnswerResponse>
 */
class EvaluateAnswerCallback implements CallbackInterface
{
    final public function endpoint(): string
    {
        return EvaluateAnswerEndpoint::class;
    }

    /**
     * @param EvaluateAnswerRequest $request
     */
    final public function request(SowisoApiContext $context, RequestInterface $request): void
    {
        $this->onRequest($context, $request);
    }

    /**
     * @param EvaluateAnswerResponse $response
     */
    final public function response(SowisoApiContext $context, ResponseInterface $response): void
    {
        $this->onResponse($context, $response);
    }

    /**
     * @param EvaluateAnswerRequest $request
     * @param EvaluateAnswerResponse $response
     */
    final public function success(
        SowisoApiContext $context,
        RequestInterface $request,
        ResponseInterface $response
    ): void {
        $this->onSuccess($context, $request, $response);
    }

    final public function failure(SowisoApiContext $context, Exception $exception): void
    {
        $this->onFailure($context, $exception);
    }

    public function onRequest(
        SowisoApiContext $context,
        EvaluateAnswerRequest $request
    ): void {
    }

    public function onResponse(
        SowisoApiContext $context,
        EvaluateAnswerResponse $response
    ): void {
    }

    public function onSuccess(
        SowisoApiContext $context,
        EvaluateAnswerRequest $request,
        EvaluateAnswerResponse $response
    ): void {
    }

    public function onFailure(
        SowisoApiContext $context,
        Exception $exception
    ): void {
    }
}

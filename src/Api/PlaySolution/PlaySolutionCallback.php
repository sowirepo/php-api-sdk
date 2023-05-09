<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution;

use Exception;
use Sowiso\SDK\Api\PlaySolution\Data\PlaySolutionOnFailureData;
use Sowiso\SDK\Api\PlaySolution\Data\PlaySolutionOnRequestData;
use Sowiso\SDK\Api\PlaySolution\Data\PlaySolutionOnResponseData;
use Sowiso\SDK\Api\PlaySolution\Data\PlaySolutionOnSuccessData;
use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionRequest;
use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements CallbackInterface<PlaySolutionRequest, PlaySolutionResponse>
 */
class PlaySolutionCallback implements CallbackInterface
{
    /**
     * @param PlaySolutionOnRequestData $data
     * @return void
     */
    public function onRequest(PlaySolutionOnRequestData $data): void
    {
    }

    /**
     * @param PlaySolutionOnResponseData $data
     * @return void
     */
    public function onResponse(PlaySolutionOnResponseData $data): void
    {
    }

    /**
     * @param PlaySolutionOnSuccessData $data
     * @return void
     */
    public function onSuccess(PlaySolutionOnSuccessData $data): void
    {
    }

    /**
     * @param PlaySolutionOnFailureData $data
     * @return void
     */
    public function onFailure(PlaySolutionOnFailureData $data): void
    {
    }

    /**
     * @return class-string<PlaySolutionEndpoint>
     */
    final public function endpoint(): string
    {
        return PlaySolutionEndpoint::class;
    }

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int
    {
        return CallbackPriority::MEDIUM;
    }

    /**
     * @param PlaySolutionRequest $request
     */
    final public function request(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request): void
    {
        $this->onRequest(new PlaySolutionOnRequestData($context, $payload, $request));
    }

    /**
     * @param PlaySolutionResponse $response
     */
    final public function response(SowisoApiContext $context, SowisoApiPayload $payload, ResponseInterface $response): void
    {
        $this->onResponse(new PlaySolutionOnResponseData($context, $payload, $response));
    }

    /**
     * @param PlaySolutionRequest $request
     * @param PlaySolutionResponse $response
     */
    final public function success(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new PlaySolutionOnSuccessData($context, $payload, $request, $response));
    }

    final public function failure(SowisoApiContext $context, SowisoApiPayload $payload, Exception $exception): void
    {
        $this->onFailure(new PlaySolutionOnFailureData($context, $payload, $exception));
    }
}

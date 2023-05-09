<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayHint;

use Exception;
use Sowiso\SDK\Api\PlayHint\Data\PlayHintOnFailureData;
use Sowiso\SDK\Api\PlayHint\Data\PlayHintOnRequestData;
use Sowiso\SDK\Api\PlayHint\Data\PlayHintOnResponseData;
use Sowiso\SDK\Api\PlayHint\Data\PlayHintOnSuccessData;
use Sowiso\SDK\Api\PlayHint\Http\PlayHintRequest;
use Sowiso\SDK\Api\PlayHint\Http\PlayHintResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements CallbackInterface<PlayHintRequest, PlayHintResponse>
 */
class PlayHintCallback implements CallbackInterface
{
    /**
     * @param PlayHintOnRequestData $data
     * @return void
     */
    public function onRequest(PlayHintOnRequestData $data): void
    {
    }

    /**
     * @param PlayHintOnResponseData $data
     * @return void
     */
    public function onResponse(PlayHintOnResponseData $data): void
    {
    }

    /**
     * @param PlayHintOnSuccessData $data
     * @return void
     */
    public function onSuccess(PlayHintOnSuccessData $data): void
    {
    }

    /**
     * @param PlayHintOnFailureData $data
     * @return void
     */
    public function onFailure(PlayHintOnFailureData $data): void
    {
    }

    /**
     * @return class-string<PlayHintEndpoint>
     */
    final public function endpoint(): string
    {
        return PlayHintEndpoint::class;
    }

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int
    {
        return CallbackPriority::MEDIUM;
    }

    /**
     * @param PlayHintRequest $request
     */
    final public function request(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request): void
    {
        $this->onRequest(new PlayHintOnRequestData($context, $payload, $request));
    }

    /**
     * @param PlayHintResponse $response
     */
    final public function response(SowisoApiContext $context, SowisoApiPayload $payload, ResponseInterface $response): void
    {
        $this->onResponse(new PlayHintOnResponseData($context, $payload, $response));
    }

    /**
     * @param PlayHintRequest $request
     * @param PlayHintResponse $response
     */
    final public function success(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new PlayHintOnSuccessData($context, $payload, $request, $response));
    }

    final public function failure(SowisoApiContext $context, SowisoApiPayload $payload, Exception $exception): void
    {
        $this->onFailure(new PlayHintOnFailureData($context, $payload, $exception));
    }
}

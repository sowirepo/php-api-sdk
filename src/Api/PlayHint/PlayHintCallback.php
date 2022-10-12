<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayHint;

use Exception;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Data\PlayHint\PlayHintOnFailureData;
use Sowiso\SDK\Data\PlayHint\PlayHintOnRequestData;
use Sowiso\SDK\Data\PlayHint\PlayHintOnResponseData;
use Sowiso\SDK\Data\PlayHint\PlayHintOnSuccessData;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

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
    final public function request(SowisoApiContext $context, RequestInterface $request): void
    {
        $this->onRequest(new PlayHintOnRequestData($context, $request));
    }

    /**
     * @param PlayHintResponse $response
     */
    final public function response(SowisoApiContext $context, ResponseInterface $response): void
    {
        $this->onResponse(new PlayHintOnResponseData($context, $response));
    }

    /**
     * @param PlayHintRequest $request
     * @param PlayHintResponse $response
     */
    final public function success(SowisoApiContext $context, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new PlayHintOnSuccessData($context, $request, $response));
    }

    final public function failure(SowisoApiContext $context, Exception $exception): void
    {
        $this->onFailure(new PlayHintOnFailureData($context, $exception));
    }
}

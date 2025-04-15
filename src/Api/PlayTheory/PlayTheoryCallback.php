<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayTheory;

use Exception;
use Sowiso\SDK\Api\PlayTheory\Data\PlayTheoryOnFailureData;
use Sowiso\SDK\Api\PlayTheory\Data\PlayTheoryOnRequestData;
use Sowiso\SDK\Api\PlayTheory\Data\PlayTheoryOnResponseData;
use Sowiso\SDK\Api\PlayTheory\Data\PlayTheoryOnSuccessData;
use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryRequest;
use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements CallbackInterface<PlayTheoryRequest, PlayTheoryResponse>
 */
class PlayTheoryCallback implements CallbackInterface
{
    /**
     * @param PlayTheoryOnRequestData $data
     * @return void
     */
    public function onRequest(PlayTheoryOnRequestData $data): void
    {
    }

    /**
     * @param PlayTheoryOnResponseData $data
     * @return void
     */
    public function onResponse(PlayTheoryOnResponseData $data): void
    {
    }

    /**
     * @param PlayTheoryOnSuccessData $data
     * @return void
     */
    public function onSuccess(PlayTheoryOnSuccessData $data): void
    {
    }

    /**
     * @param PlayTheoryOnFailureData $data
     * @return void
     */
    public function onFailure(PlayTheoryOnFailureData $data): void
    {
    }

    /**
     * @return class-string<PlayTheoryEndpoint>
     */
    final public function endpoint(): string
    {
        return PlayTheoryEndpoint::class;
    }

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int
    {
        return CallbackPriority::MEDIUM;
    }

    /**
     * @param PlayTheoryRequest $request
     */
    final public function request(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request): void
    {
        $this->onRequest(new PlayTheoryOnRequestData($context, $payload, $request));
    }

    /**
     * @param PlayTheoryResponse $response
     */
    final public function response(SowisoApiContext $context, SowisoApiPayload $payload, ResponseInterface $response): void
    {
        $this->onResponse(new PlayTheoryOnResponseData($context, $payload, $response));
    }

    /**
     * @param PlayTheoryRequest $request
     * @param PlayTheoryResponse $response
     */
    final public function success(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new PlayTheoryOnSuccessData($context, $payload, $request, $response));
    }

    final public function failure(SowisoApiContext $context, SowisoApiPayload $payload, Exception $exception): void
    {
        $this->onFailure(new PlayTheoryOnFailureData($context, $payload, $exception));
    }
}

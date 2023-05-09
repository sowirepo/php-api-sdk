<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise;

use Exception;
use Sowiso\SDK\Api\PlayExercise\Data\PlayExerciseOnFailureData;
use Sowiso\SDK\Api\PlayExercise\Data\PlayExerciseOnRequestData;
use Sowiso\SDK\Api\PlayExercise\Data\PlayExerciseOnResponseData;
use Sowiso\SDK\Api\PlayExercise\Data\PlayExerciseOnSuccessData;
use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseRequest;
use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements CallbackInterface<PlayExerciseRequest, PlayExerciseResponse>
 */
class PlayExerciseCallback implements CallbackInterface
{
    /**
     * @param PlayExerciseOnRequestData $data
     * @return void
     */
    public function onRequest(PlayExerciseOnRequestData $data): void
    {
    }

    /**
     * @param PlayExerciseOnResponseData $data
     * @return void
     */
    public function onResponse(PlayExerciseOnResponseData $data): void
    {
    }

    /**
     * @param PlayExerciseOnSuccessData $data
     * @return void
     */
    public function onSuccess(PlayExerciseOnSuccessData $data): void
    {
    }

    /**
     * @param PlayExerciseOnFailureData $data
     * @return void
     */
    public function onFailure(PlayExerciseOnFailureData $data): void
    {
    }

    /**
     * @return class-string<PlayExerciseEndpoint>
     */
    final public function endpoint(): string
    {
        return PlayExerciseEndpoint::class;
    }

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int
    {
        return CallbackPriority::MEDIUM;
    }

    /**
     * @param PlayExerciseRequest $request
     */
    final public function request(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request): void
    {
        $this->onRequest(new PlayExerciseOnRequestData($context, $payload, $request));
    }

    /**
     * @param PlayExerciseResponse $response
     */
    final public function response(SowisoApiContext $context, SowisoApiPayload $payload, ResponseInterface $response): void
    {
        $this->onResponse(new PlayExerciseOnResponseData($context, $payload, $response));
    }

    /**
     * @param PlayExerciseRequest $request
     * @param PlayExerciseResponse $response
     */
    final public function success(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new PlayExerciseOnSuccessData($context, $payload, $request, $response));
    }

    final public function failure(SowisoApiContext $context, SowisoApiPayload $payload, Exception $exception): void
    {
        $this->onFailure(new PlayExerciseOnFailureData($context, $payload, $exception));
    }
}

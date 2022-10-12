<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise;

use Exception;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Data\PlayExercise\PlayExerciseOnFailureData;
use Sowiso\SDK\Data\PlayExercise\PlayExerciseOnRequestData;
use Sowiso\SDK\Data\PlayExercise\PlayExerciseOnResponseData;
use Sowiso\SDK\Data\PlayExercise\PlayExerciseOnSuccessData;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

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
    final public function request(SowisoApiContext $context, RequestInterface $request): void
    {
        $this->onRequest(new PlayExerciseOnRequestData($context, $request));
    }

    /**
     * @param PlayExerciseResponse $response
     */
    final public function response(SowisoApiContext $context, ResponseInterface $response): void
    {
        $this->onResponse(new PlayExerciseOnResponseData($context, $response));
    }

    /**
     * @param PlayExerciseRequest $request
     * @param PlayExerciseResponse $response
     */
    final public function success(SowisoApiContext $context, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new PlayExerciseOnSuccessData($context, $request, $response));
    }

    final public function failure(SowisoApiContext $context, Exception $exception): void
    {
        $this->onFailure(new PlayExerciseOnFailureData($context, $exception));
    }
}

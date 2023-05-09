<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Exception;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnFailureData;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnRequestData;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnResponseData;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnSuccessData;
use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements CallbackInterface<PlayExerciseSetRequest, PlayExerciseSetResponse>
 */
class PlayExerciseSetCallback implements CallbackInterface
{
    /**
     * @param PlayExerciseSetOnRequestData $data
     * @return void
     */
    public function onRequest(PlayExerciseSetOnRequestData $data): void
    {
    }

    /**
     * @param PlayExerciseSetOnResponseData $data
     * @return void
     */
    public function onResponse(PlayExerciseSetOnResponseData $data): void
    {
    }

    /**
     * @param PlayExerciseSetOnSuccessData $data
     * @return void
     */
    public function onSuccess(PlayExerciseSetOnSuccessData $data): void
    {
    }

    /**
     * @param PlayExerciseSetOnFailureData $data
     * @return void
     */
    public function onFailure(PlayExerciseSetOnFailureData $data): void
    {
    }

    /**
     * @return class-string<PlayExerciseSetEndpoint>
     */
    final public function endpoint(): string
    {
        return PlayExerciseSetEndpoint::class;
    }

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int
    {
        return CallbackPriority::MEDIUM;
    }

    /**
     * @param PlayExerciseSetRequest $request
     */
    final public function request(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request): void
    {
        $this->onRequest(new PlayExerciseSetOnRequestData($context, $payload, $request));
    }

    /**
     * @param PlayExerciseSetResponse $response
     */
    final public function response(SowisoApiContext $context, SowisoApiPayload $payload, ResponseInterface $response): void
    {
        $this->onResponse(new PlayExerciseSetOnResponseData($context, $payload, $response));
    }

    /**
     * @param PlayExerciseSetRequest $request
     * @param PlayExerciseSetResponse $response
     */
    final public function success(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new PlayExerciseSetOnSuccessData($context, $payload, $request, $response));
    }

    final public function failure(SowisoApiContext $context, SowisoApiPayload $payload, Exception $exception): void
    {
        $this->onFailure(new PlayExerciseSetOnFailureData($context, $payload, $exception));
    }
}

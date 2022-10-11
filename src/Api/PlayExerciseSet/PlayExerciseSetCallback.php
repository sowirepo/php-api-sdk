<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Exception;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Data\PlayExerciseSet\PlayExerciseSetOnFailureData;
use Sowiso\SDK\Data\PlayExerciseSet\PlayExerciseSetOnRequestData;
use Sowiso\SDK\Data\PlayExerciseSet\PlayExerciseSetOnResponseData;
use Sowiso\SDK\Data\PlayExerciseSet\PlayExerciseSetOnSuccessData;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

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
     * @param PlayExerciseSetRequest $request
     */
    final public function request(SowisoApiContext $context, RequestInterface $request): void
    {
        $this->onRequest(new PlayExerciseSetOnRequestData($context, $request));
    }

    /**
     * @param PlayExerciseSetResponse $response
     */
    final public function response(SowisoApiContext $context, ResponseInterface $response): void
    {
        $this->onResponse(new PlayExerciseSetOnResponseData($context, $response));
    }

    /**
     * @param PlayExerciseSetRequest $request
     * @param PlayExerciseSetResponse $response
     */
    final public function success(SowisoApiContext $context, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new PlayExerciseSetOnSuccessData($context, $request, $response));
    }

    final public function failure(SowisoApiContext $context, Exception $exception): void
    {
        $this->onFailure(new PlayExerciseSetOnFailureData($context, $exception));
    }
}

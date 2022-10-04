<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Exception;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements CallbackInterface<PlayExerciseSetRequest, PlayExerciseSetResponse>
 */
class PlayExerciseSetCallback implements CallbackInterface
{
    final public function endpoint(): string
    {
        return PlayExerciseSetEndpoint::class;
    }

    /**
     * @param PlayExerciseSetRequest $request
     */
    final public function request(SowisoApiContext $context, RequestInterface $request): void
    {
        $this->onRequest($context, $request);
    }

    /**
     * @param PlayExerciseSetResponse $response
     */
    final public function response(SowisoApiContext $context, ResponseInterface $response): void
    {
        $this->onResponse($context, $response);
    }

    /**
     * @param PlayExerciseSetRequest $request
     * @param PlayExerciseSetResponse $response
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
        PlayExerciseSetRequest $request
    ): void {
    }

    public function onResponse(
        SowisoApiContext $context,
        PlayExerciseSetResponse $response
    ): void {
    }

    public function onSuccess(
        SowisoApiContext $context,
        PlayExerciseSetRequest $request,
        PlayExerciseSetResponse $response
    ): void {
    }

    public function onFailure(
        SowisoApiContext $context,
        Exception $exception
    ): void {
    }
}

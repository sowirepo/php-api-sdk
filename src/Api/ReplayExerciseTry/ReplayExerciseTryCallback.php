<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\ReplayExerciseTry;

use Exception;
use Sowiso\SDK\Api\ReplayExerciseTry\Data\ReplayExerciseTryOnFailureData;
use Sowiso\SDK\Api\ReplayExerciseTry\Data\ReplayExerciseTryOnRequestData;
use Sowiso\SDK\Api\ReplayExerciseTry\Data\ReplayExerciseTryOnResponseData;
use Sowiso\SDK\Api\ReplayExerciseTry\Data\ReplayExerciseTryOnSuccessData;
use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryRequest;
use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements CallbackInterface<ReplayExerciseTryRequest, ReplayExerciseTryResponse>
 */
class ReplayExerciseTryCallback implements CallbackInterface
{
    /**
     * @param ReplayExerciseTryOnRequestData $data
     * @return void
     */
    public function onRequest(ReplayExerciseTryOnRequestData $data): void
    {
    }

    /**
     * @param ReplayExerciseTryOnResponseData $data
     * @return void
     */
    public function onResponse(ReplayExerciseTryOnResponseData $data): void
    {
    }

    /**
     * @param ReplayExerciseTryOnSuccessData $data
     * @return void
     */
    public function onSuccess(ReplayExerciseTryOnSuccessData $data): void
    {
    }

    /**
     * @param ReplayExerciseTryOnFailureData $data
     * @return void
     */
    public function onFailure(ReplayExerciseTryOnFailureData $data): void
    {
    }

    /**
     * @return class-string<ReplayExerciseTryEndpoint>
     */
    final public function endpoint(): string
    {
        return ReplayExerciseTryEndpoint::class;
    }

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int
    {
        return CallbackPriority::MEDIUM;
    }

    /**
     * @param ReplayExerciseTryRequest $request
     */
    final public function request(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request): void
    {
        $this->onRequest(new ReplayExerciseTryOnRequestData($context, $payload, $request));
    }

    /**
     * @param ReplayExerciseTryResponse $response
     */
    final public function response(SowisoApiContext $context, SowisoApiPayload $payload, ResponseInterface $response): void
    {
        $this->onResponse(new ReplayExerciseTryOnResponseData($context, $payload, $response));
    }

    /**
     * @param ReplayExerciseTryRequest $request
     * @param ReplayExerciseTryResponse $response
     */
    final public function success(SowisoApiContext $context, SowisoApiPayload $payload, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new ReplayExerciseTryOnSuccessData($context, $payload, $request, $response));
    }

    final public function failure(SowisoApiContext $context, SowisoApiPayload $payload, Exception $exception): void
    {
        $this->onFailure(new ReplayExerciseTryOnFailureData($context, $payload, $exception));
    }
}

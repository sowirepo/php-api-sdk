<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer;

use Exception;
use Sowiso\SDK\Api\StoreAnswer\Data\StoreAnswerOnFailureData;
use Sowiso\SDK\Api\StoreAnswer\Data\StoreAnswerOnRequestData;
use Sowiso\SDK\Api\StoreAnswer\Data\StoreAnswerOnResponseData;
use Sowiso\SDK\Api\StoreAnswer\Data\StoreAnswerOnSuccessData;
use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerRequest;
use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

/**
 * @implements CallbackInterface<StoreAnswerRequest, StoreAnswerResponse>
 */
class StoreAnswerCallback implements CallbackInterface
{
    /**
     * @param StoreAnswerOnRequestData $data
     * @return void
     */
    public function onRequest(StoreAnswerOnRequestData $data): void
    {
    }

    /**
     * @param StoreAnswerOnResponseData $data
     * @return void
     */
    public function onResponse(StoreAnswerOnResponseData $data): void
    {
    }

    /**
     * @param StoreAnswerOnSuccessData $data
     * @return void
     */
    public function onSuccess(StoreAnswerOnSuccessData $data): void
    {
    }

    /**
     * @param StoreAnswerOnFailureData $data
     * @return void
     */
    public function onFailure(StoreAnswerOnFailureData $data): void
    {
    }

    /**
     * @return class-string<StoreAnswerEndpoint>
     */
    final public function endpoint(): string
    {
        return StoreAnswerEndpoint::class;
    }

    /**
     * @return int of {@see CallbackPriority}
     */
    public function priority(): int
    {
        return CallbackPriority::MEDIUM;
    }

    /**
     * @param StoreAnswerRequest $request
     */
    final public function request(SowisoApiContext $context, RequestInterface $request): void
    {
        $this->onRequest(new StoreAnswerOnRequestData($context, $request));
    }

    /**
     * @param StoreAnswerResponse $response
     */
    final public function response(SowisoApiContext $context, ResponseInterface $response): void
    {
        $this->onResponse(new StoreAnswerOnResponseData($context, $response));
    }

    /**
     * @param StoreAnswerRequest $request
     * @param StoreAnswerResponse $response
     */
    final public function success(SowisoApiContext $context, RequestInterface $request, ResponseInterface $response): void
    {
        $this->onSuccess(new StoreAnswerOnSuccessData($context, $request, $response));
    }

    final public function failure(SowisoApiContext $context, Exception $exception): void
    {
        $this->onFailure(new StoreAnswerOnFailureData($context, $exception));
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer;

use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerRequest;
use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerResponse;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements RequestHandlerInterface<StoreAnswerEndpoint, StoreAnswerRequest, StoreAnswerResponse>
 */
class StoreAnswerRequestHandler implements RequestHandlerInterface
{
    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param StoreAnswerRequest $request
     * @return array<string, mixed>|null
     */
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, StoreAnswerRequest $request): array|null
    {
        return null;
    }

    /**
     * @return class-string<StoreAnswerEndpoint>
     */
    final public function endpoint(): string
    {
        return StoreAnswerEndpoint::class;
    }

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param StoreAnswerEndpoint $endpoint
     * @param StoreAnswerRequest $request
     * @return StoreAnswerResponse|null
     */
    final public function handleRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        EndpointInterface $endpoint,
        RequestInterface $request,
    ): ?ResponseInterface {
        if (null === $data = $this->handle($context, $payload, $request)) {
            return null;
        }

        /** @var StoreAnswerResponse $response */
        $response = $endpoint->createResponse($context, $payload, $data, $request);

        return $response;
    }
}

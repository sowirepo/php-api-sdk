<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayHint;

use Sowiso\SDK\Api\PlayHint\Http\PlayHintRequest;
use Sowiso\SDK\Api\PlayHint\Http\PlayHintResponse;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements RequestHandlerInterface<PlayHintEndpoint, PlayHintRequest, PlayHintResponse>
 */
class PlayHintRequestHandler implements RequestHandlerInterface
{
    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlayHintRequest $request
     * @return array<string, mixed>|null
     */
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayHintRequest $request): array|null
    {
        return null;
    }

    /**
     * @return class-string<PlayHintEndpoint>
     */
    final public function endpoint(): string
    {
        return PlayHintEndpoint::class;
    }

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlayHintEndpoint $endpoint
     * @param PlayHintRequest $request
     * @return PlayHintResponse|null
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

        /** @var PlayHintResponse $response */
        $response = $endpoint->createResponse($context, $payload, $data, $request);

        return $response;
    }
}

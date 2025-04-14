<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayTheory;

use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryRequest;
use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryResponse;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements RequestHandlerInterface<PlayTheoryEndpoint, PlayTheoryRequest, PlayTheoryResponse>
 */
class PlayTheoryRequestHandler implements RequestHandlerInterface
{
    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlayTheoryRequest $request
     * @return array<string, mixed>|null
     */
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayTheoryRequest $request): array|null
    {
        return null;
    }

    /**
     * @return class-string<PlayTheoryEndpoint>
     */
    final public function endpoint(): string
    {
        return PlayTheoryEndpoint::class;
    }

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlayTheoryEndpoint $endpoint
     * @param PlayTheoryRequest $request
     * @return PlayTheoryResponse|null
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

        /** @var PlayTheoryResponse $response */
        $response = $endpoint->createResponse($context, $payload, $data, $request);

        return $response;
    }
}

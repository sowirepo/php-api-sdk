<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution;

use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionRequest;
use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionResponse;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements RequestHandlerInterface<PlaySolutionEndpoint, PlaySolutionRequest, PlaySolutionResponse>
 */
class PlaySolutionRequestHandler implements RequestHandlerInterface
{
    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlaySolutionRequest $request
     * @return array<string, mixed>|null
     */
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlaySolutionRequest $request): array|null
    {
        return null;
    }

    /**
     * @return class-string<PlaySolutionEndpoint>
     */
    final public function endpoint(): string
    {
        return PlaySolutionEndpoint::class;
    }

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlaySolutionEndpoint $endpoint
     * @param PlaySolutionRequest $request
     * @return PlaySolutionResponse|null
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

        /** @var PlaySolutionResponse $response */
        $response = $endpoint->createResponse($context, $payload, $data, $request);

        return $response;
    }
}

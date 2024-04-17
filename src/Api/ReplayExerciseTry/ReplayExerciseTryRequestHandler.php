<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\ReplayExerciseTry;

use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryRequest;
use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryResponse;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements RequestHandlerInterface<ReplayExerciseTryEndpoint, ReplayExerciseTryRequest, ReplayExerciseTryResponse>
 */
class ReplayExerciseTryRequestHandler implements RequestHandlerInterface
{
    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param ReplayExerciseTryRequest $request
     * @return array<string, mixed>|null
     */
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, ReplayExerciseTryRequest $request): array|null
    {
        return null;
    }

    /**
     * @return class-string<ReplayExerciseTryEndpoint>
     */
    final public function endpoint(): string
    {
        return ReplayExerciseTryEndpoint::class;
    }

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param ReplayExerciseTryEndpoint $endpoint
     * @param ReplayExerciseTryRequest $request
     * @return ReplayExerciseTryResponse|null
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

        /** @var ReplayExerciseTryResponse $response */
        $response = $endpoint->createResponse($context, $payload, $data, $request);

        return $response;
    }
}

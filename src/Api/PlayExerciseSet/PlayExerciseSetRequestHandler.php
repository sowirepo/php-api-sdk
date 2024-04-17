<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetResponse;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements RequestHandlerInterface<PlayExerciseSetEndpoint, PlayExerciseSetRequest, PlayExerciseSetResponse>
 */
class PlayExerciseSetRequestHandler implements RequestHandlerInterface
{
    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlayExerciseSetRequest $request
     * @return array<string, mixed>|null
     */
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseSetRequest $request): array|null
    {
        return null;
    }

    /**
     * @return class-string<PlayExerciseSetEndpoint>
     */
    final public function endpoint(): string
    {
        return PlayExerciseSetEndpoint::class;
    }

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlayExerciseSetEndpoint $endpoint
     * @param PlayExerciseSetRequest $request
     * @return PlayExerciseSetResponse|null
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

        /** @var PlayExerciseSetResponse $response */
        $response = $endpoint->createResponse($context, $payload, $data, $request);

        return $response;
    }
}

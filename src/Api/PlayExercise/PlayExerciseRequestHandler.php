<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise;

use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseRequest;
use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseResponse;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements RequestHandlerInterface<PlayExerciseEndpoint, PlayExerciseRequest, PlayExerciseResponse>
 */
class PlayExerciseRequestHandler implements RequestHandlerInterface
{
    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlayExerciseRequest $request
     * @return array<string, mixed>|null
     */
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseRequest $request): array|null
    {
        return null;
    }

    /**
     * @return class-string<PlayExerciseEndpoint>
     */
    final public function endpoint(): string
    {
        return PlayExerciseEndpoint::class;
    }

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param PlayExerciseEndpoint $endpoint
     * @param PlayExerciseRequest $request
     * @return PlayExerciseResponse|null
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

        /** @var PlayExerciseResponse $response */
        $response = $endpoint->createResponse($context, $payload, $data, $request);

        return $response;
    }
}

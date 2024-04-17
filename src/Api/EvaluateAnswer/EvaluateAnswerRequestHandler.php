<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerRequest;
use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerResponse;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\RequestHandlers\RequestHandlerInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * @implements RequestHandlerInterface<EvaluateAnswerEndpoint, EvaluateAnswerRequest, EvaluateAnswerResponse>
 */
class EvaluateAnswerRequestHandler implements RequestHandlerInterface
{
    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param EvaluateAnswerRequest $request
     * @return array<string, mixed>|null
     */
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, EvaluateAnswerRequest $request): array|null
    {
        return null;
    }

    /**
     * @return class-string<EvaluateAnswerEndpoint>
     */
    final public function endpoint(): string
    {
        return EvaluateAnswerEndpoint::class;
    }

    /**
     * @param SowisoApiContext $context
     * @param SowisoApiPayload $payload
     * @param EvaluateAnswerEndpoint $endpoint
     * @param EvaluateAnswerRequest $request
     * @return EvaluateAnswerResponse|null
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

        /** @var EvaluateAnswerResponse $response */
        $response = $endpoint->createResponse($context, $payload, $data, $request);

        return $response;
    }
}

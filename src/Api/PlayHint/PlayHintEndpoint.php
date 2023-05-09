<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayHint;

use Sowiso\SDK\Api\PlayHint\Http\PlayHintRequest;
use Sowiso\SDK\Api\PlayHint\Http\PlayHintResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlayHintEndpoint extends AbstractEndpoint
{
    public const NAME = "play/hint";

    protected function createRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
    ): RequestInterface {
        return new PlayHintRequest($context, $payload, $data);
    }

    protected function createResponse(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new PlayHintResponse($context, $payload, $data, $request);
    }
}

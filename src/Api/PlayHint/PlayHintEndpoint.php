<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayHint;

use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

class PlayHintEndpoint extends AbstractEndpoint
{
    public const NAME = "play/hint";

    protected function createRequest(
        SowisoApiContext $context,
        array $data,
    ): RequestInterface {
        return new PlayHintRequest($context, $data);
    }

    protected function createResponse(
        SowisoApiContext $context,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new PlayHintResponse($context, $data, $request);
    }
}

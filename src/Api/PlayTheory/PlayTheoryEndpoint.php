<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayTheory;

use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryRequest;
use Sowiso\SDK\Api\PlayTheory\Http\PlayTheoryResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlayTheoryEndpoint extends AbstractEndpoint
{
    public const NAME = "play/theory";

    public function createRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
    ): RequestInterface {
        return new PlayTheoryRequest($context, $payload, $data);
    }

    public function createResponse(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new PlayTheoryResponse($context, $payload, $data, $request);
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer;

use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerRequest;
use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerResponse;
use Sowiso\SDK\Endpoints\AbstractEndpoint;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class StoreAnswerEndpoint extends AbstractEndpoint
{
    public const NAME = "store/answer";

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    protected function createRequest(
        SowisoApiContext $context,
        array $data,
    ): RequestInterface {
        return new StoreAnswerRequest($context, $data);
    }

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    protected function createResponse(
        SowisoApiContext $context,
        array $data,
        RequestInterface $request,
    ): ResponseInterface {
        return new StoreAnswerResponse($context, $data, $request);
    }
}

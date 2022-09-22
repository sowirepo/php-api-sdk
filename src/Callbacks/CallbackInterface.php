<?php

declare(strict_types=1);

namespace Sowiso\SDK\Callbacks;

use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

interface CallbackInterface
{
    /**
     * @return class-string<EndpointInterface>
     */
    public function endpoint(): string;

    public function onRequest(
        SowisoApiContext $context,
        RequestInterface $request,
    ): void;

    public function onResponse(
        SowisoApiContext $context,
        ResponseInterface $response,
    ): void;

    public function onSuccess(
        SowisoApiContext $context,
        RequestInterface $request,
        ResponseInterface $response,
    ): void;

    public function onFailure(SowisoApiContext $context): void;
}

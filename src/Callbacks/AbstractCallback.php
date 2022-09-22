<?php

declare(strict_types=1);

namespace Sowiso\SDK\Callbacks;

use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\SowisoApiContext;

abstract class AbstractCallback implements CallbackInterface
{
    public function onRequest(
        SowisoApiContext $context,
        RequestInterface $request,
    ): void {
    }

    public function onResponse(
        SowisoApiContext $context,
        ResponseInterface $response,
    ): void {
    }

    public function onSuccess(
        SowisoApiContext $context,
        RequestInterface $request,
        ResponseInterface $response
    ): void {
    }

    public function onFailure(SowisoApiContext $context): void
    {
    }
}

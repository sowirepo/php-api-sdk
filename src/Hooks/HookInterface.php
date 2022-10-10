<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks;

use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;

interface HookInterface
{
    /**
     * @return array<CallbackInterface<RequestInterface, ResponseInterface>>
     */
    public function getCallbacks(): array;
}

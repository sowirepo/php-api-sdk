<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints\Http;

use Sowiso\SDK\SowisoApiContext;

abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected SowisoApiContext $context,
        protected array $data,
        protected RequestInterface $request,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints\Http;

use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected SowisoApiContext $context,
        protected SowisoApiPayload $payload,
        protected array $data,
        protected RequestInterface $request,
    ) {
    }

    /**
     * @return array<string, mixed> $data
     */
    public function getData(): array
    {
        return $this->data;
    }
}

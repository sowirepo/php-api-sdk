<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints\Http;

use Sowiso\SDK\SowisoApiContext;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected SowisoApiContext $context,
        protected array $data,
    ) {
    }

    public function getMethod(): string
    {
        return "GET";
    }

    public function getBody(): ?string
    {
        return null;
    }
}

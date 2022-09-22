<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints\Http;

interface RequestInterface
{
    public function getMethod(): string;

    public function getUri(): string;

    public function getBody(): ?string;
}

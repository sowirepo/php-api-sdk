<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints\Http;

interface RequestInterface
{
    /**
     * @return array<string, mixed>|null $data
     */
    public function getData(): ?array;

    public function getMethod(): string;

    public function getUri(): string;

    public function getBody(): ?string;
}

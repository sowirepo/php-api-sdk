<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints\Http;

interface ResponseInterface
{
    /**
     * @return array<string, mixed> $data
     */
    public function getData(): array;
}

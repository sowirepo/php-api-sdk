<?php

declare(strict_types=1);

namespace Sowiso\SDK;

class SowisoApiContext
{
    /**
     * @param array<string, mixed>|null $data
     */
    public function __construct(
        private ?array $data,
        private ?string $username,
    ) {
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public static function create(
        ?array $data = null,
        ?string $username = null,
    ): self {
        return new self($data, $username);
    }

    /**
     * @return array<string, mixed>|null $data
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}

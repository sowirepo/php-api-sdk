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
        private ?string $user,
    ) {
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public static function create(
        ?array $data = null,
        ?string $user = null,
    ): self {
        return new self($data, $user);
    }

    /**
     * @return array<string, mixed>|null $data
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }
}

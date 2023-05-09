<?php

declare(strict_types=1);

namespace Sowiso\SDK;

class SowisoApiPayload
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private ?array $data,
    ) {
    }

    /**
     * @param array<string, mixed> $json
     */
    public static function createFromRequest(array $json): self
    {
        $data = $json[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] ?? null;

        if (!is_array($data) || $data === []) {
            return new self(null);
        }

        return new self($data);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function __toString(): string
    {
        $data = json_encode($this->getData());

        if ($data === false) {
            $data = null;
        }

        return "SowisoApiPayload{data='$data'}";
    }
}

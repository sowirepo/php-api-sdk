<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints\Http;

use JsonException;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
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
        return 'GET';
    }

    public function getBody(): ?string
    {
        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function makeBody(array $data): ?string
    {
        try {
            $data = array_filter($data, fn($key) => !str_starts_with($key, '__'), ARRAY_FILTER_USE_KEY);

            if (!is_string($body = json_encode($data, flags: JSON_THROW_ON_ERROR))) {
                return null;
            }

            return $body;
        } catch (JsonException $e) {
            throw new InvalidJsonDataException($e);
        }
    }
}

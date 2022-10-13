<?php

declare(strict_types=1);

namespace Sowiso\SDK;

use Sowiso\SDK\Exceptions\InvalidBaseUrlException;
use Sowiso\SDK\Exceptions\NoApiKeyException;
use Sowiso\SDK\Exceptions\NoBaseUrlException;
use Sowiso\SDK\Exceptions\SowisoApiException;

class SowisoApiConfiguration
{
    public const ENDPOINT_IDENTIFIER = "__endpoint";
    public const API_KEY_HEADER = "X-API-KEY";

    public function __construct(
        private string $baseUrl,
        private string $apiKey,
    ) {
    }

    public static function create(string $baseUrl, string $apiKey): self
    {
        return new self($baseUrl, $apiKey);
    }

    /**
     * @throws SowisoApiException
     */
    public function validate(): void
    {
        if ($this->getBaseUrl() === '') {
            throw new NoBaseUrlException();
        }

        if (filter_var($this->getBaseUrl(), FILTER_VALIDATE_URL) === false) {
            throw new InvalidBaseUrlException();
        }

        if ($this->getApiKey() === '') {
            throw new NoApiKeyException();
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function __toString(): string
    {
        return "SowisoApiConfiguration{baseUrl='{$this->getBaseUrl()}', apiKey='{$this->getApiKey()}'}";
    }
}

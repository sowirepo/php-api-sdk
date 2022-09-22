<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use Exception;

final class ResponseErrorException extends Exception implements SowisoApiException
{
    public function __construct(string $message, private int $statusCode)
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

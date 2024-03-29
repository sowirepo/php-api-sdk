<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use InvalidArgumentException;
use Throwable;

final class InvalidJsonResponseException extends InvalidArgumentException implements SowisoApiException
{
    public function __construct(string $message, ?Throwable $cause = null)
    {
        parent::__construct(
            message: 'InvalidJsonResponse ("' . $message . '")',
            previous: $cause,
        );
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use Exception;
use Throwable;

final class DataVerificationFailedException extends Exception implements SowisoApiException
{
    public function __construct(string $message, ?Throwable $cause = null)
    {
        parent::__construct(
            message: 'DataVerificationFailed ("' . $message . '")',
            previous: $cause,
        );
    }
}

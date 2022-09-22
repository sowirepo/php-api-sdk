<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use InvalidArgumentException;
use Throwable;

final class InvalidJsonDataException extends InvalidArgumentException implements SowisoApiException
{
    public function __construct(?Throwable $cause = null)
    {
        parent::__construct(
            message: 'InvalidJsonData',
            previous: $cause,
        );
    }
}

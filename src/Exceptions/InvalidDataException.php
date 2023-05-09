<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use InvalidArgumentException;

final class InvalidDataException extends InvalidArgumentException implements SowisoApiException
{
    public function __construct(string $reason)
    {
        parent::__construct(sprintf("InvalidData '%s'", $reason));
    }

    public static function create(string $reason): InvalidDataException
    {
        return new InvalidDataException($reason);
    }
}

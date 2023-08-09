<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use InvalidArgumentException;

/**
 * @deprecated Will be removed in v1.0.0
 */
final class InvalidTryIdException extends InvalidArgumentException implements SowisoApiException
{
    public function __construct(private int $tryId)
    {
        parent::__construct(sprintf("InvalidTryId '%d'", $tryId));
    }

    public function getTryId(): int
    {
        return $this->tryId;
    }
}

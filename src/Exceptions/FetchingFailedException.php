<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use Exception;
use Throwable;

final class FetchingFailedException extends Exception implements SowisoApiException
{
    public function __construct(?Throwable $cause = null)
    {
        parent::__construct(
            message: 'FetchingFailed',
            previous: $cause,
        );
    }
}

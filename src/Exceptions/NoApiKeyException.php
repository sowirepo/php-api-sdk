<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use InvalidArgumentException;

final class NoApiKeyException extends InvalidArgumentException implements SowisoApiException
{
    public function __construct()
    {
        parent::__construct('NoApiKey');
    }
}

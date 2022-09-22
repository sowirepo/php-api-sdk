<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use InvalidArgumentException;

final class MissingDataException extends InvalidArgumentException implements SowisoApiException
{
    public function __construct(private string $field)
    {
        parent::__construct(sprintf("MissingData '%s'", $field));
    }

    /**
     * @param class-string $class
     */
    public static function create(string $class, string $field): MissingDataException
    {
        return new MissingDataException(sprintf('%s::%s', $class, $field));
    }

    public function getField(): string
    {
        return $this->field;
    }
}

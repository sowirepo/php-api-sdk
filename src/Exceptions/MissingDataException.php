<?php

declare(strict_types=1);

namespace Sowiso\SDK\Exceptions;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

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
        try {
            $className = (new ReflectionClass($class))->getShortName();
        } catch (ReflectionException) {
            $className = $class;
        }

        return new MissingDataException(sprintf('%s::%s', $className, $field));
    }

    public function getField(): string
    {
        return $this->field;
    }
}

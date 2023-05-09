<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

class Payload
{
    public const Test = [
        'test' => 'yes',
        'test:deep' => [
            '_deep' => 1,
        ],
        'test:array' => [1, 'two', 0x3],
    ];
}

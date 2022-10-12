<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

use Sowiso\SDK\Api\StoreAnswer\StoreAnswerEndpoint;

class StoreAnswer
{
    public const Uri = '/api/store/answer';

    public const Request = [
        '__endpoint' => StoreAnswerEndpoint::NAME,
        'try_id' => 12345,
    ];

    public const Response = [
        'success' => true,
    ];
}

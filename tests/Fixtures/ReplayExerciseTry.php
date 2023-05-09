<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

use Sowiso\SDK\Api\ReplayExerciseTry\ReplayExerciseTryEndpoint;

class ReplayExerciseTry
{
    public const Uri = '/api/play/replay/try_id/12345/lang/en';

    public const UriWithoutLanguage = '/api/play/replay/try_id/12345';

    public const Request = [
        '__endpoint' => ReplayExerciseTryEndpoint::NAME,
        'try_id' => 12345,
        'lang' => 'en',
    ];

    public const RequestWithoutLanguage = [
        '__endpoint' => ReplayExerciseTryEndpoint::NAME,
        'try_id' => 12345,
    ];

    public const Response = [];
}

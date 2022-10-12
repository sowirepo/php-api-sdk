<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

use Sowiso\SDK\Api\PlayHint\PlayHintEndpoint;

class PlayHint
{
    public const Uri = '/api/play/hint/try_id/12345/lang/en';

    public const UriWithoutLanguage = '/api/play/hint/try_id/12345';

    public const Request = [
        '__endpoint' => PlayHintEndpoint::NAME,
        'try_id' => 12345,
        'lang' => 'en',
    ];

    public const RequestWithoutLanguage = [
        '__endpoint' => PlayHintEndpoint::NAME,
        'try_id' => 12345,
    ];

    public const Response = [
        'hint' => 'Do <b>it</b> differently!',
        'next_hint' => false,
    ];
}

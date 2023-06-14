<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

use Sowiso\SDK\Api\PlaySolution\PlaySolutionEndpoint;

class PlaySolution
{
    public const Uri = '/api/play/solution/try_id/12345/lang/en';

    public const UriWithoutLanguage = '/api/play/solution/try_id/12345';

    public const Request = [
        '__endpoint' => PlaySolutionEndpoint::NAME,
        'try_id' => 12345,
        'lang' => 'en',
    ];

    public const RequestWithoutLanguage = [
        '__endpoint' => PlaySolutionEndpoint::NAME,
        'try_id' => 12345,
    ];

    public const Response = [
        'completed' => true,
        'set_completed' => false,
        'score' => 0.0,
        'followup' => 'This is the <b>follow-up</b> text!',
        'solution' => 'This is the <b>solution</b> text!',
        'solution_extended' => 'This is the <b>solution_extended</b> text!',
    ];

    public const ResponseForCompletedSet = [
        'completed' => true,
        'set_completed' => true,
        'score' => 0.0,
        'followup' => 'This is the <b>follow-up</b> text!',
        'solution' => 'This is the <b>solution</b> text!',
        'solution_extended' => 'This is the <b>solution_extended</b> text!',
    ];
}

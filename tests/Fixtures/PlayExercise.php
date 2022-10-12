<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

use Sowiso\SDK\Api\PlayExercise\PlayExerciseEndpoint;

class PlayExercise
{
    public const Uri = '/api/play/exercise/try_id/12345/username/user1/lang/en/view/student/arrays/true/payload/true';

    public const UriWithoutLanguage = '/api/play/exercise/try_id/12345/username/user1/view/student/arrays/true/payload/true';

    public const Request = [
        '__endpoint' => PlayExerciseEndpoint::NAME,
        'view' => 'student',
        'lang' => 'en',
        'try_id' => 12345,
    ];

    public const RequestWithoutView = [
        '__endpoint' => PlayExerciseEndpoint::NAME,
        'lang' => 'en',
        'try_id' => 12345,
    ];

    public const RequestWithoutLanguage = [
        '__endpoint' => PlayExerciseEndpoint::NAME,
        'view' => 'student',
        'try_id' => 12345,
    ];

    public const Response = [
        'try_id' => '12345',
    ];
}

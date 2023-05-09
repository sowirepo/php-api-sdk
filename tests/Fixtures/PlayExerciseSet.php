<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetEndpoint;

class PlayExerciseSet
{
    public const Uri = '/api/play/set/set_id/234/username/user1/lang/en/view/student/arrays/true/payload/true';

    public const UriAlternative = '/api/play/set/set_id/345/username/user1/lang/en/view/student/arrays/true/payload/true';

    public const UriAlternativeUser = '/api/play/set/set_id/345/username/user2/lang/en/view/student/arrays/true/payload/true';

    public const UriReadonlyView = '/api/play/set/set_id/234/username/user1/lang/en/view/readonly/arrays/true/payload/true';

    public const UriWithoutLanguage = '/api/play/set/set_id/234/username/user1/view/student/arrays/true/payload/true';

    public const UriWithTryId = '/api/play/set/try_id/12345/username/user1/lang/en/view/student/arrays/true/payload/true';
    public const UriWithTryIdWithoutLanguage = '/api/play/set/try_id/12345/username/user1/view/student/arrays/true/payload/true';

    public const Request = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'view' => 'student',
        'lang' => 'en',
        'set_id' => 234,
    ];

    public const RequestAlternative = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'view' => 'student',
        'lang' => 'en',
        'set_id' => 345,
    ];

    public const RequestReadonlyView = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'view' => 'readonly',
        'lang' => 'en',
        'set_id' => 234,
    ];

    public const RequestWithoutView = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'lang' => 'en',
        'set_id' => 234,
    ];

    public const RequestWithoutLanguage = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'view' => 'student',
        'set_id' => 234,
    ];

    public const RequestWithTryId = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'view' => 'student',
        'lang' => 'en',
        'try_id' => 12345,
    ];

    public const RequestWithTryIdWithoutView = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'lang' => 'en',
        'try_id' => 12345,
    ];

    public const RequestWithTryIdWithoutLanguage = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'view' => 'student',
        'try_id' => 12345,
    ];

    public const RequestWithSetIdAndTryId = [
        '__endpoint' => PlayExerciseSetEndpoint::NAME,
        'view' => 'student',
        'lang' => 'en',
        'set_id' => 234,
        'try_id' => 12345,
    ];

    public const Response = [
        [
            'exercise_id' => 67890,
            'try_id' => '12345',
            'set_order' => '0',
        ],
        [
            'exercise_id' => 67891,
            'try_id' => '12346',
            'set_order' => '1',
        ],
    ];

    public const ResponseOneExercise = [
        [
            'exercise_id' => 67890,
            'try_id' => '12345',
            'set_order' => '0',
        ],
    ];

    public const ResponseAlternativeOneExercise = [
        [
            'exercise_id' => 67891,
            'try_id' => '12346',
            'set_order' => '0',
        ],
    ];

    public const ResponseReadonlyView = [
        [
            'exercise_id' => 67890,
            'try_id' => null,
            'set_order' => '0',
        ],
        [
            'exercise_id' => 67891,
            'try_id' => null,
            'set_order' => '1',
        ],
    ];

    public const ResponseOneExerciseReadonlyView = [
        [
            'exercise_id' => 67890,
            'try_id' => null,
            'set_order' => '0',
        ],
    ];

    public const ResponseAlternativeExerciseReadonlyView = [
        [
            'exercise_id' => 67891,
            'try_id' => null,
            'set_order' => '0',
        ],
    ];

    public const ResponseWithTryId = [
        [
            'exercise_id' => 67890,
            'try_id' => '12345',
            'set_order' => '0',
        ],
        [
            'exercise_id' => 67891,
            'try_id' => '12346',
            'set_order' => '1',
        ],
    ];

    public const ResponseOneExerciseWithTryId = [
        [
            'exercise_id' => 67890,
            'try_id' => '12345',
            'set_order' => '0',
        ],
    ];

    public const ResponseAlternativeExerciseWithTryId = [
        [
            'exercise_id' => 67891,
            'try_id' => '12346',
            'set_order' => '0',
        ],
    ];
}

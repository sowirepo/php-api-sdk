<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerEndpoint;

class EvaluateAnswer
{
    public const Uri = '/api/evaluate/answer/try_id/12345/view/student';

    public const UriAlternative = '/api/evaluate/answer/try_id/12346/view/student';

    public const UriInTestMode = '/api/evaluate/answer/try_id/12345/view/student/mode/test_strict';

    public const Request = [
        '__endpoint' => EvaluateAnswerEndpoint::NAME,
        'try_id' => 12345,
    ];

    public const RequestAlternative = [
        '__endpoint' => EvaluateAnswerEndpoint::NAME,
        'try_id' => 12346,
    ];

    public const Response = [
        'set_completed' => false,
        'exercise_evaluation' => self::ResponseExerciseEvaluation,
        'answer_evaluation' => self::ResponseAnswerEvaluation,
    ];

    public const ResponseForCompletedSet = [
        'set_completed' => true,
        'exercise_evaluation' => self::ResponseExerciseEvaluation,
        'answer_evaluation' => self::ResponseAnswerEvaluation,
    ];

    private const ResponseExerciseEvaluation = [
        'completed' => true,
        'score' => 9.9,
        'hints' => 2,
        'solutions' => 1,
    ];

    private const ResponseAnswerEvaluation = [
        'index' => 1,
        'completed' => true,
        'passed' => true,
        'score' => 2.7,
        'feedback' => 'Great!',
    ];
}

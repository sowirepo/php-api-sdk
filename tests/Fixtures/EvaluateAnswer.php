<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Fixtures;

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerEndpoint;

class EvaluateAnswer
{
    public const Uri = '/api/evaluate/answer/try_id/1/view/student';

    public const Request = [
        '__endpoint' => EvaluateAnswerEndpoint::NAME,
        'try_id' => 1,
    ];

    public const Response = [
        'exercise_evaluation' => [
            'completed' => true,
            'score' => 9.9,
            'hints' => 2,
            'solutions' => 1,
        ],
        'answer_evaluation' => [
            'index' => 1,
            'completed' => true,
            'passed' => true,
            'score' => 2.7,
            'feedback' => 'Great!',
        ],
    ];
}
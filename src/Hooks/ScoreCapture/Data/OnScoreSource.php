<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\ScoreCapture\Data;

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerEndpoint;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionEndpoint;

final class OnScoreSource
{
    public const EVALUATE_ANSWER = EvaluateAnswerEndpoint::NAME;
    public const PLAY_SOLUTION = PlaySolutionEndpoint::NAME;
}

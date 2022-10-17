<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\ScoreCapture;

use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnSuccessData;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\PlaySolution\Data\PlaySolutionOnSuccessData;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionCallback;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Hooks\HookInterface;
use Sowiso\SDK\Hooks\ScoreCapture\Data\OnScoreData;
use Sowiso\SDK\Hooks\ScoreCapture\Data\OnScoreSource;

/**
 * The {@link ScoreCaptureHook} wraps all endpoints that return some form of score for a user.
 */
abstract class ScoreCaptureHook implements HookInterface
{
    /**
     * This method is called when a score for a "Try ID" was returned by the API.
     *
     * @param OnScoreData $data containing the current context, the "Try ID", the score,
     * whether the try was completed, and which endpoint (source) gave that score
     */
    abstract public function onScore(OnScoreData $data): void;

    /**
     * @return array<CallbackInterface<RequestInterface, ResponseInterface>>
     */
    public function getCallbacks(): array
    {
        // @phpstan-ignore-next-line
        return [
            $this->evaluateAnswerCallback(),
            $this->playSolutionCallback(),
        ];
    }

    final public function evaluateAnswerCallback(): EvaluateAnswerCallback
    {
        return new class ($this) extends EvaluateAnswerCallback {
            public function __construct(private ScoreCaptureHook $hook)
            {
            }

            public function onSuccess(EvaluateAnswerOnSuccessData $data): void
            {
                $this->hook->onScore(
                    new OnScoreData(
                        context: $data->getContext(),
                        source: OnScoreSource::EVALUATE_ANSWER,
                        tryId: $data->getRequest()->getTryId(),
                        completed: $data->getResponse()->isCompleted(),
                        score: $data->getResponse()->getScore(),
                    )
                );
            }

            public function priority(): int
            {
                return CallbackPriority::HIGH;
            }
        };
    }

    final public function playSolutionCallback(): PlaySolutionCallback
    {
        return new class ($this) extends PlaySolutionCallback {
            public function __construct(private ScoreCaptureHook $hook)
            {
            }

            public function onSuccess(PlaySolutionOnSuccessData $data): void
            {
                $this->hook->onScore(
                    new OnScoreData(
                        context: $data->getContext(),
                        source: OnScoreSource::PLAY_SOLUTION,
                        tryId: $data->getRequest()->getTryId(),
                        completed: $data->getResponse()->isCompleted(),
                        score: $data->getResponse()->getScore(),
                    )
                );
            }

            public function priority(): int
            {
                return CallbackPriority::HIGH;
            }
        };
    }
}

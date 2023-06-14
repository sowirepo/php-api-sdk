<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\TestMode;

use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnRequestData;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnRequestData;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetEndpoint;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Hooks\HookInterface;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseSetBePlayedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseTryBeEvaluatedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseTryBePlayedInTestModeData;

/**
 * The {@link TestModeHook} simplifies everything related to playing and evaluating in "test" mode.
 */
abstract class TestModeHook implements HookInterface
{
    /**
     * This method is called before a {@link PlayExerciseSetEndpoint} request is sent to the API.
     *
     * @param ShouldExerciseSetBePlayedInTestModeData $data containing the current context and the "Set ID"
     * @return bool whether the "Set ID" should be played in "test" mode
     */
    abstract public function shouldExerciseSetBePlayedInTestMode(ShouldExerciseSetBePlayedInTestModeData $data): bool;

    /**
     * This method is called before a {@link PlayExerciseSetEndpoint} request is sent to the API.
     *
     * @param ShouldExerciseTryBePlayedInTestModeData $data containing the current context and the "Try ID"
     * @return bool whether the "Try ID" should be played in "test" mode
     */
    abstract public function shouldExerciseTryBePlayedInTestMode(ShouldExerciseTryBePlayedInTestModeData $data): bool;

    /**
     * This method is called before a {@link PlayExerciseSetEndpoint} request is sent to the API.
     *
     * @param ShouldExerciseTryBeEvaluatedInTestModeData $data containing the current context and the "Try ID"
     * @return bool whether the "Try ID" should be evaluated in "test" mode
     */
    abstract public function shouldExerciseTryBeEvaluatedInTestMode(ShouldExerciseTryBeEvaluatedInTestModeData $data): bool;

    /**
     * @return array<CallbackInterface<RequestInterface, ResponseInterface>>
     */
    public function getCallbacks(): array
    {
        // @phpstan-ignore-next-line
        return [
            $this->playExerciseSetCallback(),
            $this->evaluateAnswerCallback(),
        ];
    }

    final public function playExerciseSetCallback(): PlayExerciseSetCallback
    {
        return new class ($this) extends PlayExerciseSetCallback {
            public function __construct(private TestModeHook $hook)
            {
            }

            public function onRequest(PlayExerciseSetOnRequestData $data): void
            {
                $tryId = $data->getRequest()->getTryId();
                $setId = $data->getRequest()->getSetId();

                if ($data->getRequest()->usesTryId() && $tryId !== null) {
                    $testMode = $this->hook->shouldExerciseTryBePlayedInTestMode(new ShouldExerciseTryBePlayedInTestModeData(
                        context: $data->getContext(),
                        payload: $data->getPayload(),
                        tryId: $tryId,
                    ));
                } elseif (!$data->getRequest()->usesTryId() && $setId !== null) {
                    $testMode = $this->hook->shouldExerciseSetBePlayedInTestMode(new ShouldExerciseSetBePlayedInTestModeData(
                        context: $data->getContext(),
                        payload: $data->getPayload(),
                        setId: $setId,
                    ));
                } else {
                    return;
                }

                $data->getRequest()->setTestMode($testMode);
            }
        };
    }

    final public function evaluateAnswerCallback(): EvaluateAnswerCallback
    {
        return new class ($this) extends EvaluateAnswerCallback {
            public function __construct(private TestModeHook $hook)
            {
            }

            public function onRequest(EvaluateAnswerOnRequestData $data): void
            {
                $testMode = $this->hook->shouldExerciseTryBeEvaluatedInTestMode(new ShouldExerciseTryBeEvaluatedInTestModeData(
                    context: $data->getContext(),
                    payload: $data->getPayload(),
                    tryId: $data->getRequest()->getTryId(),
                ));

                $data->getRequest()->setTestMode($testMode);
            }
        };
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks;

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerRequest;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetResponse;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\InvalidTryIdException;
use Sowiso\SDK\SowisoApiContext;

abstract class TryIdVerificationHook implements HookInterface
{
    abstract public function onRegisterTryId(SowisoApiContext $context, int $tryId): void;

    abstract public function onCatchInvalidTryId(SowisoApiContext $context, int $tryId): void;

    abstract public function isValidTryId(SowisoApiContext $context, int $tryId): bool;

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

    final public function validateTryId(SowisoApiContext $context, int $tryId): void
    {
        if ($this->isValidTryId($context, $tryId)) {
            return;
        }

        $this->onCatchInvalidTryId($context, $tryId);

        throw new InvalidTryIdException();
    }

    private function playExerciseSetCallback(): PlayExerciseSetCallback
    {
        return new class ($this) extends PlayExerciseSetCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onSuccess(
                SowisoApiContext $context,
                PlayExerciseSetRequest $request,
                PlayExerciseSetResponse $response
            ): void {
                if ($request->isReadonlyView()) {
                    return;
                }

                foreach ($response->getExerciseTries() as $exerciseTry) {
                    $this->hook->onRegisterTryId($context, $exerciseTry['tryId']);
                }
            }

            // TODO: priority()
        };
    }

    private function evaluateAnswerCallback(): EvaluateAnswerCallback
    {
        return new class ($this) extends EvaluateAnswerCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onRequest(
                SowisoApiContext $context,
                EvaluateAnswerRequest $request
            ): void {
                $this->hook->validateTryId($context, $request->getTryId());
            }
        };
    }
}

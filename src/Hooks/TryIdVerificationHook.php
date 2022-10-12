<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks;

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseCallback;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Api\PlayHint\PlayHintCallback;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Data\EvaluateAnswer\EvaluateAnswerOnRequestData;
use Sowiso\SDK\Data\PlayExercise\PlayExerciseOnRequestData;
use Sowiso\SDK\Data\PlayExerciseSet\PlayExerciseSetOnSuccessData;
use Sowiso\SDK\Data\PlayHint\PlayHintOnRequestData;
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
            $this->playExerciseCallback(),
            $this->playHintCallback(),
        ];
    }

    final public function validateTryId(SowisoApiContext $context, int $tryId): void
    {
        if ($this->isValidTryId($context, $tryId)) {
            return;
        }

        $this->onCatchInvalidTryId($context, $tryId);

        throw new InvalidTryIdException($tryId);
    }

    final public function playExerciseSetCallback(): PlayExerciseSetCallback
    {
        return new class ($this) extends PlayExerciseSetCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onSuccess(PlayExerciseSetOnSuccessData $data): void
            {
                if ($data->getRequest()->isReadonlyView()) {
                    return;
                }

                foreach ($data->getResponse()->getExerciseTries() as $exerciseTry) {
                    $this->hook->onRegisterTryId($data->getContext(), $exerciseTry['tryId']);
                }
            }

            public function priority(): int
            {
                return CallbackPriority::HIGH;
            }
        };
    }

    final public function evaluateAnswerCallback(): EvaluateAnswerCallback
    {
        return new class ($this) extends EvaluateAnswerCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onRequest(EvaluateAnswerOnRequestData $data): void
            {
                $this->hook->validateTryId($data->getContext(), $data->getRequest()->getTryId());
            }
        };
    }

    final public function playExerciseCallback(): PlayExerciseCallback
    {
        return new class ($this) extends PlayExerciseCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onRequest(PlayExerciseOnRequestData $data): void
            {
                $this->hook->validateTryId($data->getContext(), $data->getRequest()->getTryId());
            }
        };
    }

    final public function playHintCallback(): PlayHintCallback
    {
        return new class ($this) extends PlayHintCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onRequest(PlayHintOnRequestData $data): void
            {
                $this->hook->validateTryId($data->getContext(), $data->getRequest()->getTryId());
            }
        };
    }
}

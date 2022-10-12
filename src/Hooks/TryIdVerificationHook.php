<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks;

use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnRequestData;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\PlayExercise\Data\PlayExerciseOnRequestData;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseCallback;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnSuccessData;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Api\PlayHint\Data\PlayHintOnRequestData;
use Sowiso\SDK\Api\PlayHint\PlayHintCallback;
use Sowiso\SDK\Api\PlaySolution\Data\PlaySolutionOnRequestData;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionCallback;
use Sowiso\SDK\Api\StoreAnswer\Data\StoreAnswerOnRequestData;
use Sowiso\SDK\Api\StoreAnswer\StoreAnswerCallback;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
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
            $this->playSolutionCallback(),
            $this->storeAnswerCallback(),
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

    final public function playSolutionCallback(): PlaySolutionCallback
    {
        return new class ($this) extends PlaySolutionCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onRequest(PlaySolutionOnRequestData $data): void
            {
                $this->hook->validateTryId($data->getContext(), $data->getRequest()->getTryId());
            }
        };
    }

    final public function storeAnswerCallback(): StoreAnswerCallback
    {
        return new class ($this) extends StoreAnswerCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onRequest(StoreAnswerOnRequestData $data): void
            {
                $this->hook->validateTryId($data->getContext(), $data->getRequest()->getTryId());
            }
        };
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\TryIdVerification;

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
use Sowiso\SDK\Api\ReplayExerciseTry\Data\ReplayExerciseTryOnRequestData;
use Sowiso\SDK\Api\ReplayExerciseTry\ReplayExerciseTryCallback;
use Sowiso\SDK\Api\StoreAnswer\Data\StoreAnswerOnRequestData;
use Sowiso\SDK\Api\StoreAnswer\StoreAnswerCallback;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\InvalidTryIdException;
use Sowiso\SDK\Hooks\HookInterface;
use Sowiso\SDK\Hooks\TryIdVerification\Data\IsValidTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\Data\OnCatchInvalidTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\Data\OnRegisterTryIdData;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

/**
 * The {@link TryIdVerificationHook} wraps all endpoints that deal with "Try IDs".
 */
abstract class TryIdVerificationHook implements HookInterface
{
    /**
     * This method is called when a new "Try ID" was returned by the API.
     *
     * @param OnRegisterTryIdData $data containing the current context and the "Try ID"
     */
    abstract public function onRegisterTryId(OnRegisterTryIdData $data): void;

    /**
     * This method is called when an invalid "Try ID" was caught.
     *
     * @param OnCatchInvalidTryIdData $data containing the current context and the "Try ID"
     */
    abstract public function onCatchInvalidTryId(OnCatchInvalidTryIdData $data): void;

    /**
     * This method is called before requests that contain a "Try ID" are passed to the API.
     *
     * @param IsValidTryIdData $data containing the current context and the "Try ID"
     * whether the "Try ID" is valid
     */
    abstract public function isValidTryId(IsValidTryIdData $data): bool;

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
            $this->replayExerciseTryCallback(),
            $this->playHintCallback(),
            $this->playSolutionCallback(),
            $this->storeAnswerCallback(),
        ];
    }

    final public function validateTryId(SowisoApiContext $context, SowisoApiPayload $payload, int $tryId): void
    {
        if ($this->isValidTryId(new IsValidTryIdData($context, $payload, $tryId))) {
            return;
        }

        $this->onCatchInvalidTryId(new OnCatchInvalidTryIdData($context, $payload, $tryId));

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

                if ($data->getRequest()->usesTryId()) {
                    // Safe to cast to an int because we checked if the try_id is used or not
                    $tryId = (int) $data->getRequest()->getTryId();

                    $this->hook->validateTryId($data->getContext(), $data->getPayload(), $tryId);

                    return;
                }

                foreach ($data->getResponse()->getExerciseTries() as $exerciseTry) {
                    $this->hook->onRegisterTryId(
                        new OnRegisterTryIdData(
                            $data->getContext(),
                            $data->getPayload(),
                            $exerciseTry['tryId'],
                        )
                    );
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
                $this->hook->validateTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
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
                $this->hook->validateTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
            }
        };
    }

    final public function replayExerciseTryCallback(): ReplayExerciseTryCallback
    {
        return new class ($this) extends ReplayExerciseTryCallback {
            public function __construct(private TryIdVerificationHook $hook)
            {
            }

            public function onRequest(ReplayExerciseTryOnRequestData $data): void
            {
                $this->hook->validateTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
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
                $this->hook->validateTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
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
                $this->hook->validateTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
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
                $this->hook->validateTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
            }
        };
    }
}

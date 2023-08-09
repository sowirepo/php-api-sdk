<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\DataVerification;

use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnRequestData;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\PlayExercise\Data\PlayExerciseOnRequestData;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseCallback;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnRequestData;
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
use Sowiso\SDK\Exceptions\DataVerificationFailedException;
use Sowiso\SDK\Hooks\HookInterface;

/**
 * The {@link DataVerificationHook} simplifies the verification of data in requests before they are passed to the API.
 */
abstract class DataVerificationHook implements HookInterface
{
    /**
     * This method is called before {@link PlayExerciseSetEndpoint} requests are passed to the API.
     *
     * @param PlayExerciseSetOnRequestData $data containing the current context, the payload, and all the request data
     * @throws DataVerificationFailedException when verifying the request failed
     */
    abstract public function verifyPlayExerciseSetRequest(PlayExerciseSetOnRequestData $data): void;

    /**
     * This method is called before {@link PlayExerciseEndpoint} requests are passed to the API.
     *
     * @param PlayExerciseOnRequestData $data containing the current context, the payload, and all the request data
     * @throws DataVerificationFailedException when verifying the request failed
     */
    abstract public function verifyPlayExerciseRequest(PlayExerciseOnRequestData $data): void;

    /**
     * This method is called before {@link ReplayExerciseTryEndpoint} requests are passed to the API.
     *
     * @param ReplayExerciseTryOnRequestData $data containing the current context, the payload, and all the request data
     * @throws DataVerificationFailedException when verifying the request failed
     */
    abstract public function verifyReplayExerciseTryRequest(ReplayExerciseTryOnRequestData $data): void;

    /**
     * This method is called before {@link EvaluateAnswerEndpoint} requests are passed to the API.
     *
     * @param EvaluateAnswerOnRequestData $data containing the current context, the payload, and all the request data
     * @throws DataVerificationFailedException when verifying the request failed
     */
    abstract public function verifyEvaluateAnswerRequest(EvaluateAnswerOnRequestData $data): void;

    /**
     * This method is called before {@link PlayHintEndpoint} requests are passed to the API.
     *
     * @param PlayHintOnRequestData $data containing the current context, the payload, and all the request data
     * @throws DataVerificationFailedException when verifying the request failed
     */
    abstract public function verifyPlayHintRequest(PlayHintOnRequestData $data): void;

    /**
     * This method is called before {@link PlaySolutionEndpoint} requests are passed to the API.
     *
     * @param PlaySolutionOnRequestData $data containing the current context, the payload, and all the request data
     * @throws DataVerificationFailedException when verifying the request failed
     */
    abstract public function verifyPlaySolutionRequest(PlaySolutionOnRequestData $data): void;

    /**
     * This method is called before {@link StoreAnswerEndpoint} requests are passed to the API.
     *
     * @param StoreAnswerOnRequestData $data containing the current context, the payload, and all the request data
     * @throws DataVerificationFailedException when verifying the request failed
     */
    abstract public function verifyStoreAnswerRequest(StoreAnswerOnRequestData $data): void;

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

    final public function playExerciseSetCallback(): PlayExerciseSetCallback
    {
        return new class ($this) extends PlayExerciseSetCallback {
            public function __construct(private DataVerificationHook $hook)
            {
            }

            public function onRequest(PlayExerciseSetOnRequestData $data): void
            {
                $this->hook->verifyPlayExerciseSetRequest($data);
            }

            public function priority(): int
            {
                return CallbackPriority::HIGH;
            }
        };
    }

    final public function playExerciseCallback(): PlayExerciseCallback
    {
        return new class ($this) extends PlayExerciseCallback {
            public function __construct(private DataVerificationHook $hook)
            {
            }

            public function onRequest(PlayExerciseOnRequestData $data): void
            {
                $this->hook->verifyPlayExerciseRequest($data);
            }

            public function priority(): int
            {
                return CallbackPriority::HIGH;
            }
        };
    }

    final public function replayExerciseTryCallback(): ReplayExerciseTryCallback
    {
        return new class ($this) extends ReplayExerciseTryCallback {
            public function __construct(private DataVerificationHook $hook)
            {
            }

            public function onRequest(ReplayExerciseTryOnRequestData $data): void
            {
                $this->hook->verifyReplayExerciseTryRequest($data);
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
            public function __construct(private DataVerificationHook $hook)
            {
            }

            public function onRequest(EvaluateAnswerOnRequestData $data): void
            {
                $this->hook->verifyEvaluateAnswerRequest($data);
            }

            public function priority(): int
            {
                return CallbackPriority::HIGH;
            }
        };
    }

    final public function playHintCallback(): PlayHintCallback
    {
        return new class ($this) extends PlayHintCallback {
            public function __construct(private DataVerificationHook $hook)
            {
            }

            public function onRequest(PlayHintOnRequestData $data): void
            {
                $this->hook->verifyPlayHintRequest($data);
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
            public function __construct(private DataVerificationHook $hook)
            {
            }

            public function onRequest(PlaySolutionOnRequestData $data): void
            {
                $this->hook->verifyPlaySolutionRequest($data);
            }

            public function priority(): int
            {
                return CallbackPriority::HIGH;
            }
        };
    }

    final public function storeAnswerCallback(): StoreAnswerCallback
    {
        return new class ($this) extends StoreAnswerCallback {
            public function __construct(private DataVerificationHook $hook)
            {
            }

            public function onRequest(StoreAnswerOnRequestData $data): void
            {
                $this->hook->verifyStoreAnswerRequest($data);
            }

            public function priority(): int
            {
                return CallbackPriority::HIGH;
            }
        };
    }
}

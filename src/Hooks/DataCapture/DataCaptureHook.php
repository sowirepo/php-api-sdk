<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\DataCapture;

use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnSuccessData;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Hooks\DataCapture\Data\OnRegisterExerciseSetData;
use Sowiso\SDK\Hooks\DataCapture\Data\OnRegisterExerciseTryData;
use Sowiso\SDK\Hooks\HookInterface;

/**
 * The {@link DataCaptureHook} simplifies receiving common, processed data.
 */
abstract class DataCaptureHook implements HookInterface
{
    /**
     * This method is called when a new set of "Exercise Tries" was returned by the API.
     *
     * @param OnRegisterExerciseSetData $data containing the current context, the "Set ID", the "Exercise Tries"
     */
    abstract public function onRegisterExerciseSet(OnRegisterExerciseSetData $data): void;

    /**
     * @return array<CallbackInterface<RequestInterface, ResponseInterface>>
     */
    public function getCallbacks(): array
    {
        // @phpstan-ignore-next-line
        return [
            $this->playExerciseSetCallback(),
        ];
    }

    final public function playExerciseSetCallback(): PlayExerciseSetCallback
    {
        return new class ($this) extends PlayExerciseSetCallback {
            public function __construct(private DataCaptureHook $hook)
            {
            }

            public function onSuccess(PlayExerciseSetOnSuccessData $data): void
            {
                if ($data->getRequest()->isReadonlyView()) {
                    return;
                }

                if ($data->getRequest()->usesTryId()) {
                    return;
                }

                // Safe to cast to an int because when no try_id is used, the set_id was already validated
                $setId = (int) $data->getRequest()->getSetId();

                $this->hook->onRegisterExerciseSet(
                    new OnRegisterExerciseSetData(
                        $data->getContext(),
                        $data->getPayload(),
                        $setId,
                        $data->getResponse()->getExerciseTries(),
                    )
                );
            }

            public function priority(): int
            {
                return CallbackPriority::HIGHER;
            }
        };
    }
}

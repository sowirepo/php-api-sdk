<?php

declare(strict_types=1);

namespace Sowiso\SDK\Hooks\DataCapture;

use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnSuccessData;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Hooks\DataCapture\Data\OnRegisterExerciseTryData;
use Sowiso\SDK\Hooks\HookInterface;

/**
 * The {@link DataCaptureHook} simplifies receiving common, processed data.
 */
abstract class DataCaptureHook implements HookInterface
{
    /**
     * This method is called when a new "Exercise Try" was returned by the API.
     *
     * @param OnRegisterExerciseTryData $data containing the current context, the "Set ID", the "Exercise ID", and the "Try ID"
     */
    abstract public function onRegisterExerciseTry(OnRegisterExerciseTryData $data): void;

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

                foreach ($data->getResponse()->getExerciseTries() as $exerciseTry) {
                    $this->hook->onRegisterExerciseTry(
                        new OnRegisterExerciseTryData(
                            $data->getContext(),
                            $data->getRequest()->getSetId(),
                            $exerciseTry['exerciseId'],
                            $exerciseTry['tryId'],
                        )
                    );
                }
            }

            public function priority(): int
            {
                return CallbackPriority::HIGHER;
            }
        };
    }
}

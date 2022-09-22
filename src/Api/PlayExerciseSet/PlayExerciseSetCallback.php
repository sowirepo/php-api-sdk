<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Sowiso\SDK\Callbacks\AbstractCallback;

class PlayExerciseSetCallback extends AbstractCallback
{
    public function endpoint(): string
    {
        return PlayExerciseSetEndpoint::class;
    }
}

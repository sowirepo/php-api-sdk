<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;

class PlayExerciseSetRequest extends AbstractRequest
{
    public function getUri(): string
    {
        return "/test";
    }
}

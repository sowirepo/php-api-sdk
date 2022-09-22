<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Sowiso\SDK\Callbacks\AbstractCallback;

class EvaluateAnswerCallback extends AbstractCallback
{
    public function endpoint(): string
    {
        return EvaluateAnswerEndpoint::class;
    }
}

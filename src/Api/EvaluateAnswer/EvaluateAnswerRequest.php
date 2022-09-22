<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\SowisoApiContext;

class EvaluateAnswerRequest extends AbstractRequest
{
    private int $tryId;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(SowisoApiContext $context, array $data)
    {
        parent::__construct($context, $data);

        $this->tryId = 0;
    }

    public function getUri(): string
    {
        return "/test";
    }

    public function getTryId(): int
    {
        return $this->tryId;
    }
}

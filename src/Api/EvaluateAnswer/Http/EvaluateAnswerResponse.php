<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer\Http;

use Sowiso\SDK\Endpoints\Http\AbstractResponse;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class EvaluateAnswerResponse extends AbstractResponse
{
    private bool $completed;

    private bool $setCompleted;

    private float $score;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, SowisoApiPayload $payload, array $data, RequestInterface $request)
    {
        parent::__construct($context, $payload, $data, $request);

        /** @var array<string, mixed> $exerciseEvaluation */
        $exerciseEvaluation = $data['exercise_evaluation'] ?? [];

        if (null === ($completed = $exerciseEvaluation['completed'] ?? null) || !is_bool($completed)) {
            throw MissingDataException::create(self::class, 'completed');
        }

        if (null === ($setCompleted = $data['set_completed'] ?? null) || !is_bool($setCompleted)) {
            throw MissingDataException::create(self::class, 'setCompleted');
        }

        if (null === ($score = $exerciseEvaluation['score'] ?? null) || !is_numeric($score)) {
            throw MissingDataException::create(self::class, 'score');
        }

        $this->completed = $completed;
        $this->setCompleted = $setCompleted;
        $this->score = (float) $score;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function isSetCompleted(): bool
    {
        return $this->setCompleted;
    }

    public function getScore(): float
    {
        return $this->score;
    }
}

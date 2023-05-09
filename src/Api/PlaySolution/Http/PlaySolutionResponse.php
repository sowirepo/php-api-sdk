<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution\Http;

use Sowiso\SDK\Endpoints\Http\AbstractResponse;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlaySolutionResponse extends AbstractResponse
{
    private bool $completed;

    private float $score;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, SowisoApiPayload $payload, array $data, RequestInterface $request)
    {
        parent::__construct($context, $payload, $data, $request);

        if (null === ($completed = $data['completed'] ?? null) || !is_bool($completed)) {
            throw MissingDataException::create(self::class, 'completed');
        }

        if (null === ($score = $data['score'] ?? null) || !is_numeric($score)) {
            throw MissingDataException::create(self::class, 'score');
        }

        $this->completed = $completed;
        $this->score = (float) $score;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function getScore(): float
    {
        return $this->score;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution\Http;

use Sowiso\SDK\Endpoints\Http\AbstractResponse;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class PlaySolutionResponse extends AbstractResponse
{
    private bool $completed;

    private float $score;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, array $data, RequestInterface $request)
    {
        parent::__construct($context, $data, $request);

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

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer;

use Sowiso\SDK\Endpoints\Http\AbstractResponse;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class EvaluateAnswerResponse extends AbstractResponse
{
    private int $tryId;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, array $data, RequestInterface $request)
    {
        parent::__construct($context, $data, $request);

        if (null === ($tryId = $data['tryId'] ?? null) || !is_int($tryId)) {
            throw MissingDataException::create(self::class, 'tryId');
        }

        $this->tryId = $tryId;
    }

    public function getTryId(): int
    {
        return $this->tryId;
    }
}

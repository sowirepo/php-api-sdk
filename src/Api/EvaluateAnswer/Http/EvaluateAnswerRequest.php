<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\EvaluateAnswer\Http;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class EvaluateAnswerRequest extends AbstractRequest
{
    private const MODE_PRACTICE = 'practice';
    private const MODE_TEST = 'test';

    private int $tryId;

    private ?string $mode;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, SowisoApiPayload $payload, array $data)
    {
        parent::__construct($context, $payload, $data);

        if (null === ($tryId = $data['try_id'] ?? null) || !is_int($tryId)) {
            throw MissingDataException::create(self::class, 'tryId');
        }

        $this->tryId = $tryId;
        $this->mode = null;
    }

    public function getUri(): string
    {
        $uri = '/api/evaluate/answer';

        $uri .= sprintf('/try_id/%d', $this->tryId);
        $uri .= '/view/student';

        if ($this->mode === self::MODE_TEST) {
            $uri .= '/mode/test_strict';
        }

        return $uri;
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getBody(): ?string
    {
        return $this->makeBody($this->data);
    }

    public function getTryId(): int
    {
        return $this->tryId;
    }

    public function getMode(): string
    {
        return $this->mode ?? self::MODE_PRACTICE;
    }

    public function setTestMode(bool $testMode = true): void
    {
        $this->mode = $testMode ? self::MODE_TEST : null;
    }
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer\Http;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class StoreAnswerRequest extends AbstractRequest
{
    private int $tryId;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, array $data)
    {
        parent::__construct($context, $data);

        if (null === ($tryId = $data['try_id'] ?? null) || !is_int($tryId)) {
            throw MissingDataException::create(self::class, 'tryId');
        }

        $this->tryId = $tryId;
    }

    public function getUri(): string
    {
        return '/api/store/answer';
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
}

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\ReplayExerciseTry\Http;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class ReplayExerciseTryRequest extends AbstractRequest
{
    private ?string $language;

    private int $tryId;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, SowisoApiPayload $payload, array $data)
    {
        parent::__construct($context, $payload, $data);

        $language = is_string($language = $data['lang'] ?? null) ? $language : null;

        if (null === ($tryId = $data['try_id'] ?? null) || !is_int($tryId)) {
            throw MissingDataException::create(self::class, 'tryId');
        }

        $this->language = $language;
        $this->tryId = $tryId;
    }

    public function getUri(): string
    {
        $uri = '/api/play/replay';

        $uri .= sprintf('/try_id/%d', $this->tryId);

        if ($this->language !== null) {
            $uri .= sprintf('/lang/%s', $this->language);
        }

        $uri .= '/arrays/true';

        return $uri;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getTryId(): int
    {
        return $this->tryId;
    }
}

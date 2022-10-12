<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlaySolution;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class PlaySolutionRequest extends AbstractRequest
{
    private ?string $language;

    private int $tryId;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, array $data)
    {
        parent::__construct($context, $data);

        $language = is_string($language = $data['lang'] ?? null) ? $language : null;

        if (null === ($tryId = $data['try_id'] ?? null) || !is_int($tryId)) {
            throw MissingDataException::create(self::class, 'tryId');
        }

        $this->language = $language;
        $this->tryId = $tryId;
    }

    public function getUri(): string
    {
        $uri = '/api/play/solution';

        $uri .= sprintf('/try_id/%d', $this->tryId);

        if ($this->language !== null) {
            $uri .= sprintf('/lang/%s', $this->language);
        }

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

<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayTheory\Http;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\NoUserException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlayTheoryRequest extends AbstractRequest
{
    private ?string $language;

    private string $user;

    private int $theoryId;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, SowisoApiPayload $payload, array $data)
    {
        parent::__construct($context, $payload, $data);

        if (null === ($user = $context->getUser()) || trim($user) === '') {
            throw new NoUserException();
        }

        $this->user = $user;

        $language = is_string($language = $data['lang'] ?? null) ? $language : null;

        if (null === ($theoryId = $data['theory_id'] ?? null) || !is_int($theoryId)) {
            throw MissingDataException::create(self::class, 'theoryId');
        }

        $this->language = $language;
        $this->theoryId = $theoryId;
    }

    public function getUri(): string
    {
        $uri = '/api/content/theory';

        $uri .= sprintf('/theory_id/%d', $this->theoryId);

        if ($this->language !== null) {
            $uri .= sprintf('/lang/%s', $this->language);
        }

        $uri .= sprintf('/username/%s', self::encodeForUrl($this->user));

        return $uri;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getTheoryId(): int
    {
        return $this->theoryId;
    }
}

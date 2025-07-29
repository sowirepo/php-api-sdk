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
    private const MODE_FULL = 'full';
    private const MODE_QUESTION = 'question';

    private int $tryId;

    private ?string $language;

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

        $language = is_string($language = $data['lang'] ?? null) ? $language : null;
        $mode = is_string($mode = $data['mode'] ?? null) ? $mode : null;

        $this->language = $language;
        $this->tryId = $tryId;
        $this->mode = $this->validatedMode($mode);
    }

    public function getUri(): string
    {
        $uri = '/api/play/replay';

        $uri .= sprintf('/try_id/%d', $this->tryId);

        if ($this->language !== null) {
            $uri .= sprintf('/lang/%s', $this->language);
        }

        // We don't send the mode to the API, yet. We only need it for the hooks as of now.

        $uri .= '/arrays/true';

        return $uri;
    }

    public function getTryId(): int
    {
        return $this->tryId;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getMode(): string
    {
        return $this->mode ?? self::MODE_FULL;
    }

    public function usesQuestionView(): bool
    {
        return $this->getMode() === self::MODE_QUESTION;
    }

    protected function validatedMode(?string $value): ?string
    {
        $isValid = in_array($value, [
            self::MODE_FULL,
            self::MODE_QUESTION,
        ], strict: true);

        if ($isValid) {
            return $value;
        }

        return self::MODE_FULL;
    }
}

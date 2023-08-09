<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExercise\Http;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\NoUserException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlayExerciseRequest extends AbstractRequest
{
    private const VIEW_STUDENT = 'student';

    private string $user;

    private ?string $language;

    private ?string $view;

    private int $tryId;

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

        $language = is_string($language = $data['lang'] ?? null) ? $language : null;
        $view = is_string($view = $data['view'] ?? null) ? $view : null;

        if (null === ($tryId = $data['try_id'] ?? null) || !is_int($tryId)) {
            throw MissingDataException::create(self::class, 'tryId');
        }

        $this->user = $user;
        $this->language = $language;
        $this->view = $this->validatedView($view);
        $this->tryId = $tryId;
    }

    public function getUri(): string
    {
        $uri = '/api/play/exercise';

        $uri .= sprintf('/try_id/%d', $this->tryId);
        $uri .= sprintf('/username/%s', self::encodeForUrl($this->user));

        if ($this->language !== null) {
            $uri .= sprintf('/lang/%s', $this->language);
        }

        $uri .= sprintf('/view/%s', $this->view ?? self::VIEW_STUDENT);

        $uri .= '/arrays/true';
        $uri .= '/payload/true';

        return $uri;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getView(): string
    {
        return $this->view ?? self::VIEW_STUDENT;
    }

    public function getTryId(): int
    {
        return $this->tryId;
    }

    protected function validatedView(?string $value): ?string
    {
        $isValid = in_array($value, [
            self::VIEW_STUDENT,
        ], strict: true);

        if ($isValid) {
            return $value;
        }

        return self::VIEW_STUDENT;
    }
}

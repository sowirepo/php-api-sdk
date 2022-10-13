<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet\Http;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\NoUserException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class PlayExerciseSetRequest extends AbstractRequest
{
    private const VIEW_STUDENT = 'student';
    private const VIEW_READONLY = 'readonly';

    private string $user;

    private ?string $language;

    private ?string $view;

    private int $setId;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, array $data)
    {
        parent::__construct($context, $data);

        if (null === ($user = $context->getUser()) || trim($user) === '') {
            throw new NoUserException();
        }

        $language = is_string($language = $data['lang'] ?? null) ? $language : null;
        $view = is_string($view = $data['view'] ?? null) ? $view : null;

        if (null === ($setId = $data['set_id'] ?? null) || !is_int($setId)) {
            throw MissingDataException::create(self::class, 'setId');
        }

        $this->user = $user;
        $this->language = $language;
        $this->view = $view;
        $this->setId = $setId;
    }

    public function getUri(): string
    {
        $uri = '/api/play/set';

        $uri .= sprintf('/set_id/%d', $this->setId);
        $uri .= sprintf('/username/%s', $this->user);

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

    public function getSetId(): int
    {
        return $this->setId;
    }

    public function isReadonlyView(): bool
    {
        return $this->getView() === self::VIEW_READONLY;
    }
}

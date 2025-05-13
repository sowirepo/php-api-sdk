<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet\Http;

use Sowiso\SDK\Endpoints\Http\AbstractRequest;
use Sowiso\SDK\Exceptions\InvalidDataException;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\NoUserException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlayExerciseSetRequest extends AbstractRequest
{
    private const VIEW_STUDENT = 'student';
    private const VIEW_READONLY = 'readonly';
    private const VIEW_READONLY_RESTRICTED = 'readonly-restricted';
    private const MODE_PRACTICE = 'practice';
    private const MODE_TEST = 'test';
    private const MODE_PRINT = 'print';

    private string $user;

    private ?string $language;

    private ?string $view;

    private ?string $mode;

    private ?int $setId;

    private ?int $tryId;

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
        $mode = is_string($mode = $data['mode'] ?? null) ? $mode : null;

        $setId = is_int($setId = $data['set_id'] ?? null) ? $setId : null;
        $tryId = is_int($tryId = $data['try_id'] ?? null) ? $tryId : null;

        if ($mode === self::MODE_TEST) {
            throw InvalidDataException::create('mode=test supplied, use TestModeHook instead');
        }

        if ($tryId === null && $setId === null) {
            throw MissingDataException::create(self::class, 'setId');
        }

        if ($tryId !== null && $setId !== null) {
            throw InvalidDataException::create('setId and tryId supplied');
        }

        $this->user = $user;
        $this->language = $language;
        $this->view = $this->validatedView($view);
        $this->mode = $this->validatedMode($mode);
        $this->setId = $setId;
        $this->tryId = $tryId;
    }

    public function getUri(): string
    {
        $uri = '/api/play/set';

        if ($this->usesTryId()) {
            $uri .= sprintf('/try_id/%d', $this->tryId);
        } else {
            $uri .= sprintf('/set_id/%d', $this->setId);
        }

        $uri .= sprintf('/username/%s', self::encodeForUrl($this->user));

        if ($this->language !== null) {
            $uri .= sprintf('/lang/%s', $this->language);
        }

        $uri .= sprintf('/view/%s', $this->view ?? self::VIEW_STUDENT);

        if ($this->mode === self::MODE_TEST) {
            $uri .= '/mode/test_strict';
        }

        $singleExerciseMode = $this->usesTryId()
            && in_array($this->mode, [
                self::MODE_TEST,
                self::MODE_PRINT,
            ], strict: true);

        if ($singleExerciseMode) {
            $uri .= '/single_exercise/true';
        }

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

    public function getMode(): string
    {
        return $this->mode ?? self::MODE_PRACTICE;
    }

    public function getSetId(): ?int
    {
        return $this->setId;
    }

    public function getTryId(): ?int
    {
        return $this->tryId;
    }

    public function setTestMode(bool $testMode = true): void
    {
        // When the "test" mode is being deactivated even though not activated before, do nothing.

        if ($testMode) {
            $this->mode = self::MODE_TEST;
        } else {
            $this->mode = ($this->mode === self::MODE_TEST) ? null : $this->mode;
        }
    }

    public function usesAnyReadonlyView(): bool
    {
        return $this->getView() === self::VIEW_READONLY || $this->getView() === self::VIEW_READONLY_RESTRICTED;
    }

    public function usesTryId(): bool
    {
        return $this->getTryId() !== null;
    }

    protected function validatedView(?string $value): ?string
    {
        $isValid = in_array($value, [
            self::VIEW_STUDENT,
            self::VIEW_READONLY,
            self::VIEW_READONLY_RESTRICTED,
        ], strict: true);

        if ($isValid) {
            return $value;
        }

        return self::VIEW_STUDENT;
    }

    protected function validatedMode(?string $value): ?string
    {
        $isValid = in_array($value, [
            self::MODE_PRACTICE,
            self::MODE_PRINT,
        ], strict: true);

        if ($isValid) {
            return $value;
        }

        return self::MODE_PRACTICE;
    }
}

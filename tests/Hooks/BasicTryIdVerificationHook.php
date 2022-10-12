<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Hooks;

use Sowiso\SDK\Hooks\TryIdVerificationHook;
use Sowiso\SDK\SowisoApiContext;

class BasicTryIdVerificationHook extends TryIdVerificationHook
{
    /** @var array<int, array{name: string, validated: bool}> */
    private array $users = [];

    public function onRegisterTryId(SowisoApiContext $context, int $tryId): void
    {
        $this->users[$tryId] = ['name' => $context->getUsername(), 'validated' => false];
    }

    public function onCatchInvalidTryId(SowisoApiContext $context, int $tryId): void
    {
    }

    public function isValidTryId(SowisoApiContext $context, int $tryId): bool
    {
        if (null === $user = $this->users[$tryId] ?? null) {
            return false;
        }

        if ($user['name'] !== $context->getUsername()) {
            return false;
        }

        if ($user['validated'] !== false) {
            return false;
        }

        $this->users[$tryId]['validated'] = true;

        return true;
    }

    /**
     * @return array<int, array{user: string, validated: bool}>
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param int $tryId
     * @return string|null
     */
    public function getUserNameFor(int $tryId): ?string
    {
        return $this->users[$tryId]['name'] ?? null;
    }

    /**
     * @param int $tryId
     * @return bool|null
     */
    public function getUserValidatedFor(int $tryId): ?bool
    {
        return $this->users[$tryId]['validated'] ?? null;
    }
}

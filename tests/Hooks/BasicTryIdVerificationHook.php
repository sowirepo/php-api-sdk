<?php

declare(strict_types=1);

namespace Sowiso\SDK\Tests\Hooks;

use Sowiso\SDK\Hooks\TryIdVerification\Data\IsValidTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\Data\OnCatchInvalidTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\Data\OnRegisterTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\TryIdVerificationHook;

class BasicTryIdVerificationHook extends TryIdVerificationHook
{
    /** @var array<int, array{name: string, validated: bool}> */
    private array $users = [];

    public function onRegisterTryId(OnRegisterTryIdData $data): void
    {
        $this->users[$data->getTryId()] = ['name' => $data->getContext()->getUser(), 'validated' => false];
    }

    public function onCatchInvalidTryId(OnCatchInvalidTryIdData $data): void
    {
    }

    public function isValidTryId(IsValidTryIdData $data): bool
    {
        if (null === $user = $this->users[$data->getTryId()] ?? null) {
            return false;
        }

        if ($user['name'] !== $data->getContext()->getUser()) {
            return false;
        }

        if ($user['validated'] !== false) {
            return false;
        }

        $this->users[$data->getTryId()]['validated'] = true;

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

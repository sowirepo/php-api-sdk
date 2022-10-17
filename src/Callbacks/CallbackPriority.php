<?php

declare(strict_types=1);

namespace Sowiso\SDK\Callbacks;

/**
 * Any callback implementation can optionally change its priority. The higher the priority, the earlier that callback
 * implementation is called. Especially useful when defining multiple implementations for a callback, and in hooks.
 */
final class CallbackPriority
{
    public const HIGHER = 4;
    public const HIGH = 3;
    public const MEDIUM = 2;
    public const LOW = 1;
}

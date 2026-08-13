<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Env gate for Settings → Symfony debug toolbar.
 *
 * @see docs/plan/Settings_Symfony_Debug_Toolbar.md
 */
final class SymfonyDebugToolbarSupport
{
    /** @var list<string> */
    public const EDITABLE_ENVIRONMENTS = ['dev', 'stage'];

    public static function isEditable(string $environment): bool
    {
        return \in_array($environment, self::EDITABLE_ENVIRONMENTS, true);
    }
}

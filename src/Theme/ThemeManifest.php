<?php

declare(strict_types=1);

namespace App\Theme;

/**
 * Parsed theme.json (runtime contract for shipped + uploaded packages).
 */
final readonly class ThemeManifest
{
    public function __construct(
        public string $id,
        public string $name,
        public string $version,
        public string $engine,
        public ?string $requires = null,
    ) {
    }

    /**
     * @param mixed $data Decoded JSON
     */
    public static function tryFrom(mixed $data): ?self
    {
        if (!\is_array($data)) {
            return null;
        }

        $id = strtolower(trim((string) ($data['id'] ?? '')));
        $name = trim((string) ($data['name'] ?? ''));
        $version = trim((string) ($data['version'] ?? ''));
        $engine = trim((string) ($data['engine'] ?? ''));
        $requires = \array_key_exists('requires', $data)
            ? trim((string) $data['requires'])
            : null;

        if ('' === $id || 1 !== preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id)) {
            return null;
        }
        if (in_array('', [$name, $version, $engine], true)) {
            return null;
        }
        if (null !== $requires && '' === $requires) {
            $requires = null;
        }

        return new self($id, $name, $version, $engine, $requires);
    }
}

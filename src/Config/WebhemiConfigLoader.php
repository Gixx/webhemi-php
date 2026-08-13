<?php

declare(strict_types=1);

namespace App\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads / persists install-global config from var/config/webhemi.yaml.
 * Missing file → defaults (path admin). Invalid values → InvalidArgumentException.
 */
final class WebhemiConfigLoader
{
    private const RELATIVE_PATH = 'var/config/webhemi.yaml';

    private ?WebhemiConfig $cached = null;

    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    public function configPath(): string
    {
        return $this->projectDir . '/' . self::RELATIVE_PATH;
    }

    public function get(): WebhemiConfig
    {
        return $this->cached ??= $this->loadFromDisk();
    }

    public function reload(): WebhemiConfig
    {
        $this->cached = null;

        return $this->get();
    }

    public function save(WebhemiConfig $config): void
    {
        $path = $this->configPath();
        $dir = \dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Unable to create config directory "%s".', $dir));
        }

        $yaml = Yaml::dump($config->toArray(), 4, 2);
        $header = "# WebHemi install config (not in git). See config/webhemi.yaml.dist\n"
            . "# access.admin: path | domain\n"
            . "# symfony.debug_toolbar: bool (editable in dev/stage only)\n\n";
        $tmp = $path . '.tmp.' . bin2hex(random_bytes(4));
        if (false === file_put_contents($tmp, $header . $yaml)) {
            throw new \RuntimeException(sprintf('Unable to write config temp file "%s".', $tmp));
        }
        if (!rename($tmp, $path)) {
            @unlink($tmp);
            throw new \RuntimeException(sprintf('Unable to replace config file "%s".', $path));
        }

        $this->cached = $config;
    }

    /**
     * Write defaults (or given config) only if the file does not exist yet.
     */
    public function ensureFileExists(?WebhemiConfig $config = null): WebhemiConfig
    {
        if (is_file($this->configPath())) {
            return $this->get();
        }

        $config ??= WebhemiConfig::defaults();
        $this->save($config);

        return $config;
    }

    private function loadFromDisk(): WebhemiConfig
    {
        $path = $this->configPath();
        if (!is_file($path)) {
            return WebhemiConfig::defaults();
        }

        try {
            $parsed = Yaml::parseFile($path);
        } catch (ParseException $e) {
            throw new \InvalidArgumentException(
                sprintf('Invalid YAML in "%s": %s', $path, $e->getMessage()),
                0,
                $e,
            );
        }

        if (!\is_array($parsed)) {
            throw new \InvalidArgumentException(sprintf('Config root in "%s" must be a mapping.', $path));
        }

        return $this->fromArray($parsed, $path);
    }

    /**
     * @param array<mixed> $data
     */
    public function fromArray(array $data, ?string $source = null): WebhemiConfig
    {
        $defaults = WebhemiConfig::defaults();
        $source ??= 'config array';
        $root = $data['webhemi'] ?? $data;
        if (!\is_array($root)) {
            throw new \InvalidArgumentException(sprintf('"%s": webhemi key must be a mapping.', $source));
        }

        $access = $root['access'] ?? [];
        if (!\is_array($access)) {
            throw new \InvalidArgumentException(sprintf('"%s": webhemi.access must be a mapping.', $source));
        }

        $adminRaw = $access['admin'] ?? $defaults->adminAccess->value;
        if (!\is_string($adminRaw) && !\is_int($adminRaw)) {
            throw new \InvalidArgumentException(sprintf('"%s": webhemi.access.admin must be a string.', $source));
        }
        $adminAccess = AdminAccessMode::tryFrom(strtolower(trim((string) $adminRaw)));
        if (null === $adminAccess) {
            throw new \InvalidArgumentException(sprintf(
                '"%s": webhemi.access.admin must be "path" or "domain", got "%s".',
                $source,
                (string) $adminRaw,
            ));
        }

        $paths = $root['paths'] ?? [];
        if (!\is_array($paths)) {
            throw new \InvalidArgumentException(sprintf('"%s": webhemi.paths must be a mapping.', $source));
        }

        $symfony = $root['symfony'] ?? [];
        if (!\is_array($symfony)) {
            throw new \InvalidArgumentException(sprintf('"%s": webhemi.symfony must be a mapping.', $source));
        }

        return new WebhemiConfig(
            adminAccess: $adminAccess,
            adminPath: $this->normalizePath(
                $paths['admin'] ?? $defaults->adminPath,
                'admin',
                $source,
            ),
            adminApiPath: $this->normalizePath(
                $paths['admin_api'] ?? $defaults->adminApiPath,
                'admin_api',
                $source,
            ),
            publicApiPath: $this->normalizePath(
                $paths['public_api'] ?? $defaults->publicApiPath,
                'public_api',
                $source,
            ),
            loginPath: $this->normalizePath(
                $paths['login'] ?? $defaults->loginPath,
                'login',
                $source,
            ),
            registerPath: $this->normalizePath(
                $paths['register'] ?? $defaults->registerPath,
                'register',
                $source,
            ),
            symfonyDebugToolbar: $this->normalizeBool(
                $symfony['debug_toolbar'] ?? $defaults->symfonyDebugToolbar,
                'debug_toolbar',
                $source,
            ),
        );
    }

    private function normalizeBool(mixed $value, string $key, string $source): bool
    {
        if (\is_bool($value)) {
            return $value;
        }
        if (\is_int($value) && ($value === 0 || $value === 1)) {
            return $value === 1;
        }
        if (\is_string($value)) {
            $normalized = strtolower(trim($value));
            if (\in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }
            if (\in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
                return false;
            }
        }

        throw new \InvalidArgumentException(sprintf(
            '"%s": webhemi.symfony.%s must be a boolean.',
            $source,
            $key,
        ));
    }

    private function normalizePath(mixed $value, string $key, string $source): string
    {
        if (!\is_string($value) && !\is_int($value)) {
            throw new \InvalidArgumentException(sprintf('"%s": webhemi.paths.%s must be a string.', $source, $key));
        }
        $path = trim((string) $value);
        if ($path === '' || !str_starts_with($path, '/')) {
            throw new \InvalidArgumentException(sprintf(
                '"%s": webhemi.paths.%s must be an absolute path starting with "/", got "%s".',
                $source,
                $key,
                $path,
            ));
        }
        if (str_ends_with($path, '/') && $path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}

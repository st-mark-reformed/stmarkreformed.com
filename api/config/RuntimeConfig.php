<?php

declare(strict_types=1);

namespace Config;

use RuntimeException;

use function file_exists;
use function file_get_contents;
use function getenv;
use function implode;
use function trim;

readonly class RuntimeConfig
{
    public function __construct(private string $secretPath = '/run/secrets')
    {
    }

    public function getString(
        RuntimeConfigOptions $from,
        string|null $default = null,
    ): string {
        return (string) $this->getValue($from, $default);
    }

    public function getBoolean(
        RuntimeConfigOptions $from,
        bool|null $default = null,
    ): bool {
        return (bool) $this->getValue($from, $default);
    }

    public function getInteger(
        RuntimeConfigOptions $from,
        int|null $default = null,
    ): int {
        return (int) $this->getValue($from, $default);
    }

    private function getValue(
        RuntimeConfigOptions $from,
        string|bool|int|null $default = null,
    ): bool|int|string {
        $fromGetEnv = getenv($from->name);

        if ($fromGetEnv !== false) {
            return $fromGetEnv;
        }

        $fromSecret = $this->getValueFromSecretIfExists($from);

        if ($fromSecret !== false) {
            return $fromSecret;
        }

        if ($default !== null) {
            return $default;
        }

        throw new RuntimeException(implode(' ', [
            $from->name,
            'could not be found in secrets or environment variables',
            'and no default value was provided',
        ]));
    }

    private function getValueFromSecretIfExists(
        RuntimeConfigOptions $from,
    ): string|false {
        $secretPath = $this->secretPath . '/' . $from->name;

        if (! file_exists($secretPath)) {
            return false;
        }

        return trim((string) file_get_contents($secretPath));
    }
}

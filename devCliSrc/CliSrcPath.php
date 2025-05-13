<?php

declare(strict_types=1);

namespace Cli;

use function dirname;
use function ltrim;
use function rtrim;
use function trim;

readonly class CliSrcPath
{
    public const string PATH = __DIR__;

    public function projectRoot(): string
    {
        return dirname(self::PATH);
    }

    public function pathFromProjectRoot(string $path): string
    {
        return $this->projectRoot() . '/' . trim($path, '/');
    }

    public function dockerPath(string $path = ''): string
    {
        $path = rtrim('docker/' . $path, '/');

        return $this->pathFromProjectRoot($path);
    }

    public function dockerEphemeralStoragePath(string $path = ''): string
    {
        $path = rtrim('_ephemeral-storage/' . $path, '/');

        return $this->dockerPath($path);
    }

    public function dockerfileHashesPath(string $path = ''): string
    {
        $path = rtrim('dockerfile-hashes/' . $path, '/');

        return $this->dockerEphemeralStoragePath($path);
    }

    public function apiPath(string $path = ''): string
    {
        $path = rtrim(
            'api/' . ltrim($path, '/'),
            '/',
        );

        return $this->pathFromProjectRoot($path);
    }
}

<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence\Persist;

use function file_exists;
use function glob;
use function is_dir;
use function rmdir;
use function sprintf;
use function unlink;

readonly class InternalMessageAudioFileStorage
{
    private const string BASE_PATH = '/var/www/filesAboveWebroot/internal-audio';

    public function delete(string $slug, string $fileName): void
    {
        if ($slug === '' || $fileName === '') {
            return;
        }

        $directory        = sprintf('%s/%s', self::BASE_PATH, $slug);
        $absoluteFilePath = sprintf('%s/%s', $directory, $fileName);

        if (file_exists($absoluteFilePath)) {
            unlink($absoluteFilePath);
        }

        if (! is_dir($directory)) {
            return;
        }

        if (glob($directory . '/*') !== []) {
            return;
        }

        rmdir($directory);
    }
}

<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Persist;

use function file_exists;
use function unlink;

readonly class MessageAudioFileStorage
{
    public function delete(string $absoluteFilePath): void
    {
        if (! file_exists($absoluteFilePath)) {
            return;
        }

        unlink($absoluteFilePath);
    }
}

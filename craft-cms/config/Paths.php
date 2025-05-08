<?php

declare(strict_types=1);

namespace Config;

use function is_dir;
use function mkdir;

class Paths
{
    public function imapAttachmentsPath(): string
    {
        $path = CRAFT_BASE_PATH . '/storage/imap-attachments';

        if (! is_dir($path)) {
            mkdir($path, 0777);
        }

        return $path;
    }
}

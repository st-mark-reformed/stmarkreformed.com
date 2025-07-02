<?php

declare(strict_types=1);

namespace App\Messages\ImportFromCraft;

use App\StorageHandler\StorageHandler;

readonly class ImportStorageHandler
{
    private const string PATH = 'import-from-craft/';

    public function __construct(private StorageHandler $storageHandler)
    {
    }

    public function uidHasAlreadyImported(string $uid): bool
    {
        return $this->storageHandler->fileExists(
            self::PATH . 'imported-uids/' . $uid,
        );
    }

    public function writeUidAsImported(string $uid): void
    {
        $this->storageHandler->write(
            self::PATH . 'imported-uids/' . $uid,
            $uid,
        );
    }
}

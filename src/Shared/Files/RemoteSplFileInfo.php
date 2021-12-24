<?php

declare(strict_types=1);

namespace App\Shared\Files;

use SplFileInfo;

class RemoteSplFileInfo extends SplFileInfo
{
    public function __construct(
        string $filename,
        private int $size,
        private string $content,
    ) {
        parent::__construct($filename);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPerms(): int
    {
        return 0;
    }

    public function getInode(): int
    {
        return 0;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getOwner(): int
    {
        return 0;
    }

    public function getGroup(): int
    {
        return 0;
    }

    public function getATime(): int
    {
        return 0;
    }

    public function getMTime(): int
    {
        return 0;
    }

    public function getCTime(): int
    {
        return 0;
    }
}

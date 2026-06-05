<?php

declare(strict_types=1);

namespace App\Resources;

use JsonSerializable;

readonly class ResourceDownload implements JsonSerializable
{
    public function __construct(public string $filename = '')
    {
    }

    /** @return array{filename: string} */
    public function asArray(): array
    {
        return ['filename' => $this->filename];
    }

    /** @return array{filename: string} */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }
}

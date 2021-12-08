<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

class ResourceItem
{
    public function __construct(
        private string $title,
        private string $url,
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function url(): string
    {
        return $this->url;
    }
}

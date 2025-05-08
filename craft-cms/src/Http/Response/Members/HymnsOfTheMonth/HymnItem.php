<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

class HymnItem
{
    public function __construct(
        private string $href,
        private string $title,
        private string $content,
    ) {
    }

    public function href(): string
    {
        return $this->href;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function content(): string
    {
        return $this->content;
    }
}

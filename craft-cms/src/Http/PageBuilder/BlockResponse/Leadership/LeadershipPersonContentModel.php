<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\Leadership;

use Twig\Markup;

class LeadershipPersonContentModel
{
    public function __construct(
        private string $imageUrl,
        private string $title,
        private Markup $content,
    ) {
    }

    public function imageUrl(): string
    {
        return $this->imageUrl;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function content(): Markup
    {
        return $this->content;
    }
}

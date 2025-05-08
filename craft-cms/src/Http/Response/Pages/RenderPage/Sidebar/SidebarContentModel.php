<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage\Sidebar;

class SidebarContentModel
{
    public function __construct(
        private string $content,
        private string $href,
        private bool $isActive,
    ) {
    }

    public function content(): string
    {
        return $this->content;
    }

    public function href(): string
    {
        return $this->href;
    }

    public function active(): bool
    {
        return $this->isActive;
    }
}

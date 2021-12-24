<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\Menu;

use function array_map;
use function count;

class MenuItem
{
    /** @var MenuItem[] */
    private array $submenu = [];

    /**
     * @param MenuItem[] $submenu
     */
    public function __construct(
        private string $content,
        private string $href,
        array $submenu = [],
    ) {
        array_map(
            [$this, 'addItem'],
            $submenu,
        );
    }

    private function addItem(MenuItem $menuItem): void
    {
        $this->submenu[] = $menuItem;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function href(): string
    {
        return $this->href;
    }

    /**
     * @return MenuItem[]
     */
    public function submenu(): array
    {
        return $this->submenu;
    }

    public function hasSubMenu(): bool
    {
        return count($this->submenu()) > 0;
    }
}

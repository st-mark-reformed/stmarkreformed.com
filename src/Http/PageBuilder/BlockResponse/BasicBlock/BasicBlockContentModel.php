<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\BasicBlock;

use App\Http\Components\Link\Link;
use Twig\Markup;

use function array_map;
use function count;

class BasicBlockContentModel
{
    /** @var Link[] */
    private array $ctas = [];

    /**
     * @param Link[] $ctas
     */
    public function __construct(
        private string $tailwindBackgroundColor,
        private bool $noTopSpace,
        private string $alignment,
        private string $preHeadline,
        private string $headline,
        private Markup $content,
        array $ctas = [],
    ) {
        array_map(
            [$this, 'addCta'],
            $ctas,
        );
    }

    private function addCta(Link $link): void
    {
        $this->ctas[] = $link;
    }

    public function tailwindBackgroundColor(): string
    {
        return $this->tailwindBackgroundColor;
    }

    public function noTopSpace(): bool
    {
        return $this->noTopSpace;
    }

    public function alignment(): string
    {
        return $this->alignment;
    }

    public function preHeadline(): string
    {
        return $this->preHeadline;
    }

    public function headline(): string
    {
        return $this->headline;
    }

    public function content(): Markup
    {
        return $this->content;
    }

    /**
     * @return Link[]
     */
    public function ctas(): array
    {
        return $this->ctas;
    }

    public function hasCtas(): bool
    {
        return count($this->ctas) > 0;
    }
}

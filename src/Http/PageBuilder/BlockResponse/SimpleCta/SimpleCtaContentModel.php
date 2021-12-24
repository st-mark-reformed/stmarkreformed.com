<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\SimpleCta;

use App\Http\Components\Link\Link;
use Twig\Markup;

use function array_map;
use function count;

class SimpleCtaContentModel
{
    /** @var Link[] */
    private array $ctas = [];

    /**
     * @param Link[] $ctas
     */
    public function __construct(
        private string $tailwindBackgroundColor,
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

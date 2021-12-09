<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\BasicEntryBlock;

use App\Http\Components\Link\Link;
use Twig\Markup;

use function array_map;
use function count;

class BasicEntryBlockContentModel
{
    /** @var Link[] */
    private array $ctas = [];

    /**
     * @param Link[] $ctas
     */
    public function __construct(
        private string $headline,
        private string $subHeadline,
        private Markup $content,
        array $ctas = [],
    ) {
        array_map(
            function (Link $link): void {
                $this->ctas[] = $link;
            },
            $ctas,
        );
    }

    public function headline(): string
    {
        return $this->headline;
    }

    public function subHeadline(): string
    {
        return $this->subHeadline;
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

    /**
     * @return mixed[]
     */
    public function mapCtas(callable $callable): array
    {
        return array_map($callable, $this->ctas());
    }
}

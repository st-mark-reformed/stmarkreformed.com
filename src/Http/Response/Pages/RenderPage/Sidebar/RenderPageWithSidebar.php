<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage\Sidebar;

use App\Http\Components\Hero\Hero;
use App\Http\Entities\Meta;
use App\Http\Response\Pages\RenderPage\RenderPageContract;
use App\Shared\Entries\EntryHandler;
use craft\elements\Entry;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class RenderPageWithSidebar implements RenderPageContract
{
    public function __construct(
        private Meta $meta,
        private Hero $hero,
        private Entry $entry,
        private string $contentString,
        private TwigEnvironment $twig,
        private EntryHandler $entryHandler,
        private BuildSidebar $buildSidebar,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(): string
    {
        $rootEntry = $this->entryHandler->getRootEntry(
            entry: $this->entry,
        );

        $sideBarMarkup = $this->buildSidebar->fromRootEntry(
            rootEntry: $rootEntry,
            activeEntry: $this->entry,
        );

        return $this->twig->render(
            '@app/Http/Response/Pages/RenderPage/Sidebar/PageWithSidebar.twig',
            [
                'meta' => $this->meta,
                'hero' => $this->hero,
                'content' => new Markup(
                    $this->contentString,
                    'UTF-8',
                ),
                'sideBarMarkup' => $sideBarMarkup,
            ],
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage\Sidebar;

use craft\elements\Entry;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

use function array_map;
use function array_merge;

/**
 * @psalm-suppress ArgumentTypeCoercion
 * @psalm-suppress PossiblyInvalidMethodCall
 */
class BuildSidebar
{
    public function __construct(private TwigEnvironment $twig)
    {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function fromRootEntry(Entry $rootEntry, Entry $activeEntry): Markup
    {
        $sidebarItems = array_map(
            static function (Entry $entry) use (
                $activeEntry,
            ): SidebarContentModel {
                return new SidebarContentModel(
                    content: (string) $entry->title,
                    href: (string) $entry->getUrl(),
                    isActive: $entry->getId() === $activeEntry->getId(),
                );
            },
            array_merge(
                [$rootEntry],
                $rootEntry->getChildren()->all(),
            ),
        );

        return new Markup(
            $this->twig->render(
                '@app/Http/Response/Pages/RenderPage/Sidebar/Sidebar.twig',
                ['sidebarItems' => $sidebarItems]
            ),
            'UTF-8',
        );
    }
}

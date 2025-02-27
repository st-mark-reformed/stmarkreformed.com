<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\Menu;

use craft\elements\Entry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function array_filter;
use function array_values;

class MenuTwigExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'mainMenu',
                [$this, 'mainMenu']
            ),
            new TwigFunction(
                'secondaryMenu',
                [$this, 'secondaryMenu']
            ),
        ];
    }

    /**
     * @return MenuItem[]
     */
    public function mainMenu(): array
    {
        $resourcesCount = (int) Entry::find()
            ->section('resources')
            ->limit(1)
            ->count();

        return [
            new MenuItem(
                content: 'Calendar',
                href: '/calendar',
            ),
            new MenuItem(
                content: 'About',
                href: '/about',
                submenu: [
                    new MenuItem(
                        content: 'About',
                        href: '/about',
                    ),
                    // new MenuItem(
                    //     content: 'Ministries',
                    //     href: '/ministries',
                    // ),
                    new MenuItem(
                        content: 'Mission Statement',
                        href: '/about/mission-statement',
                    ),
                    new MenuItem(
                        content: 'Liturgy and Sacraments',
                        href: '/about/liturgy-and-sacraments',
                    ),
                    new MenuItem(
                        content: 'Leadership',
                        href: '/about/leadership',
                    ),
                    new MenuItem(
                        content: 'Church Government',
                        href: '/about/church-government',
                    ),
                    new MenuItem(
                        content: 'Membership',
                        href: '/about/membership',
                    ),
                    new MenuItem(
                        content: 'Connections and Associations',
                        href: '/about/connections',
                    ),
                ],
            ),
            new MenuItem(
                content: 'Media',
                href: '/media/messages',
                submenu: array_values(array_filter(
                    [
                        new MenuItem(
                            content: 'Messages',
                            href: '/media/messages',
                        ),
                        new MenuItem(
                            content: 'Galleries',
                            href: '/media/galleries',
                        ),
                        $resourcesCount > 0 ? new MenuItem(
                            content: 'Resources',
                            href: '/resources',
                        ) : null,
                        new MenuItem(
                            content: 'News',
                            href: '/news',
                        ),
                        new MenuItem(
                            content: 'Men of the Mark',
                            href: '/publications/men-of-the-mark',
                        ),
                    ],
                    static function (MenuItem|null $item): bool {
                        return $item !== null;
                    }
                )),
            ),
            new MenuItem(
                content: 'Building Fund',
                href: '/building-fund',
            ),
            new MenuItem(
                content: 'Contact',
                href: '/contact',
            ),
        ];
    }

    /**
     * @return MenuItem[]
     */
    public function secondaryMenu(): array
    {
        return [
            new MenuItem(
                content: 'Members',
                href: '/members',
            ),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\Menu;

use PHPUnit\Framework\TestCase;

use function assert;
use function is_array;

class MenuTwigExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $menuTwigExtension = new MenuTwigExtension();

        $functions = $menuTwigExtension->getFunctions();

        self::assertCount(2, $functions);

        self::assertSame(
            'mainMenu',
            $functions[0]->getName(),
        );

        $mainMenuCallable = $functions[0]->getCallable();

        assert(is_array($mainMenuCallable));

        self::assertCount(2, $mainMenuCallable);

        self::assertSame(
            $menuTwigExtension,
            $mainMenuCallable[0],
        );

        self::assertSame(
            'mainMenu',
            $mainMenuCallable[1],
        );

        self::assertSame(
            'secondaryMenu',
            $functions[1]->getName(),
        );

        $secondaryMenuCallable = $functions[1]->getCallable();

        assert(is_array($secondaryMenuCallable));

        self::assertCount(2, $secondaryMenuCallable);

        self::assertSame(
            $menuTwigExtension,
            $secondaryMenuCallable[0],
        );

        self::assertSame(
            'secondaryMenu',
            $secondaryMenuCallable[1],
        );
    }

    public function testMainMenu(): void
    {
        $menuTwigExtension = new MenuTwigExtension();

        $mainMenu = $menuTwigExtension->mainMenu();

        self::assertCount(5, $mainMenu);

        /**
         * Ministries
         */
        self::assertSame(
            'Ministries',
            $mainMenu[0]->content(),
        );
        self::assertSame(
            '/ministries',
            $mainMenu[0]->href(),
        );
        self::assertCount(
            0,
            $mainMenu[0]->submenu(),
        );
        self::assertFalse($mainMenu[0]->hasSubMenu());

        /**
         * About
         */
        self::assertSame(
            'About',
            $mainMenu[1]->content(),
        );
        self::assertSame(
            '/about',
            $mainMenu[1]->href(),
        );
        self::assertCount(
            6,
            $mainMenu[1]->submenu(),
        );
        self::assertTrue($mainMenu[1]->hasSubMenu());

        $aboutSubMenu = $mainMenu[1]->submenu();

        // /about/about
        self::assertSame(
            'About',
            $aboutSubMenu[0]->content(),
        );
        self::assertSame(
            '/about',
            $aboutSubMenu[0]->href(),
        );
        self::assertCount(
            0,
            $aboutSubMenu[0]->submenu(),
        );
        self::assertFalse($aboutSubMenu[0]->hasSubMenu());

        // /about/mission-statement
        self::assertSame(
            'Mission Statement',
            $aboutSubMenu[1]->content(),
        );
        self::assertSame(
            '/about/mission-statement',
            $aboutSubMenu[1]->href(),
        );
        self::assertCount(
            0,
            $aboutSubMenu[1]->submenu(),
        );
        self::assertFalse($aboutSubMenu[1]->hasSubMenu());

        // /about/liturgy-and-sacraments
        self::assertSame(
            'Liturgy and Sacraments',
            $aboutSubMenu[2]->content(),
        );
        self::assertSame(
            '/about/liturgy-and-sacraments',
            $aboutSubMenu[2]->href(),
        );
        self::assertCount(
            0,
            $aboutSubMenu[2]->submenu(),
        );
        self::assertFalse($aboutSubMenu[2]->hasSubMenu());

        // /about/leadership
        self::assertSame(
            'Leadership',
            $aboutSubMenu[3]->content(),
        );
        self::assertSame(
            '/about/leadership',
            $aboutSubMenu[3]->href(),
        );
        self::assertCount(
            0,
            $aboutSubMenu[3]->submenu(),
        );
        self::assertFalse($aboutSubMenu[3]->hasSubMenu());

        // /about/church-government
        self::assertSame(
            'Church Government',
            $aboutSubMenu[4]->content(),
        );
        self::assertSame(
            '/about/church-government',
            $aboutSubMenu[4]->href(),
        );
        self::assertCount(
            0,
            $aboutSubMenu[4]->submenu(),
        );
        self::assertFalse($aboutSubMenu[4]->hasSubMenu());

        // /about/membership
        self::assertSame(
            'Membership',
            $aboutSubMenu[5]->content(),
        );
        self::assertSame(
            '/about/membership',
            $aboutSubMenu[5]->href(),
        );
        self::assertCount(
            0,
            $aboutSubMenu[5]->submenu(),
        );
        self::assertFalse($aboutSubMenu[5]->hasSubMenu());

        /**
         * Media
         */
        self::assertSame(
            'Media',
            $mainMenu[2]->content(),
        );
        self::assertSame(
            '/media/messages',
            $mainMenu[2]->href(),
        );
        self::assertCount(
            3,
            $mainMenu[2]->submenu(),
        );
        self::assertTrue($mainMenu[2]->hasSubMenu());

        $mediaSubMenu = $mainMenu[2]->submenu();

        // /media/messages
        self::assertSame(
            'Messages',
            $mediaSubMenu[0]->content(),
        );
        self::assertSame(
            '/media/messages',
            $mediaSubMenu[0]->href(),
        );
        self::assertCount(
            0,
            $mediaSubMenu[0]->submenu(),
        );
        self::assertFalse($mediaSubMenu[0]->hasSubMenu());

        // /media/galleries
        self::assertSame(
            'Galleries',
            $mediaSubMenu[1]->content(),
        );
        self::assertSame(
            '/media/galleries',
            $mediaSubMenu[1]->href(),
        );
        self::assertCount(
            0,
            $mediaSubMenu[1]->submenu(),
        );
        self::assertFalse($mediaSubMenu[1]->hasSubMenu());

        // /resources
        self::assertSame(
            'Resources',
            $mediaSubMenu[2]->content(),
        );
        self::assertSame(
            '/resources',
            $mediaSubMenu[2]->href(),
        );
        self::assertCount(
            0,
            $mediaSubMenu[2]->submenu(),
        );
        self::assertFalse($mediaSubMenu[2]->hasSubMenu());

        /**
         * Building Fund
         */
        self::assertSame(
            'Building Fund',
            $mainMenu[3]->content(),
        );
        self::assertSame(
            '/building-fund',
            $mainMenu[3]->href(),
        );
        self::assertCount(
            0,
            $mainMenu[3]->submenu(),
        );
        self::assertFalse($mainMenu[3]->hasSubMenu());

        /**
         * Building Fund
         */
        self::assertSame(
            'Contact',
            $mainMenu[4]->content(),
        );
        self::assertSame(
            '/contact',
            $mainMenu[4]->href(),
        );
        self::assertCount(
            0,
            $mainMenu[4]->submenu(),
        );
        self::assertFalse($mainMenu[4]->hasSubMenu());
    }

    public function testSecondaryMenu(): void
    {
        $menuTwigExtension = new MenuTwigExtension();

        $secondaryMenu = $menuTwigExtension->secondaryMenu();

        self::assertCount(1, $secondaryMenu);

        self::assertSame(
            'Members',
            $secondaryMenu[0]->content(),
        );
        self::assertSame(
            '/members',
            $secondaryMenu[0]->href(),
        );
        self::assertCount(
            0,
            $secondaryMenu[0]->submenu(),
        );
        self::assertFalse($secondaryMenu[0]->hasSubMenu());
    }
}

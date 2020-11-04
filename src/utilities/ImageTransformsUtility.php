<?php

namespace src\utilities;

use Craft;
use craft\base\UtilityInterface;

class ImageTransformsUtility implements UtilityInterface
{
    public static function displayName(): string
    {
        return 'Image Transforms Utility';
    }

    public static function id(): string
    {
        return 'image-transforms-utility';
    }

    public static function iconPath()
    {
        return Craft::getAlias('@dev/icons/transform.svg');
    }

    public static function badgeCount(): int
    {
        return 0;
    }

    public static function contentHtml(): string
    {
        $view = Craft::$app->getView();

        return $view->renderString(
            file_get_contents(
                Craft::getAlias('@dev/cpviews/ImageTransformsUtility.twig')
            )
        );
    }

    public static function isSelectable(): bool
    {
        return false;
    }

    public static function toolbarHtml(): string
    {
        return '';
    }

    public static function footerHtml(): string
    {
        return '';
    }
}

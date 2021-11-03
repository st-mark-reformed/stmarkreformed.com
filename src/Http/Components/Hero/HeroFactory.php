<?php

declare(strict_types=1);

namespace App\Http\Components\Hero;

use App\Http\Components\Link\LinkFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\LinkField\LinkFieldHandler;
use App\Templating\TwigExtensions\HeroImage\GetDefaultHeroImageUrl;
use App\Templating\TwigExtensions\HeroImage\GetDefaultHeroOverlayOpacity;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

class HeroFactory
{
    public function __construct(
        private LinkFactory $linkFactory,
        private GenericHandler $genericHandler,
        private LinkFieldHandler $linkFieldHandler,
        private AssetsFieldHandler $assetsFieldHandler,
        private GetDefaultHeroImageUrl $defaultImageUrl,
        private GetDefaultHeroOverlayOpacity $defaultOverlayOpacity,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function createFromEntry(Entry $entry): Hero
    {
        $heroImageAsset = $this->assetsFieldHandler->getOneOrNull(
            element: $entry,
            field: 'heroImage',
        );

        $heroUpperCta = $this->linkFieldHandler->getModel(
            element: $entry,
            field: 'heroUpperCta',
        );

        $heroHeading = $this->genericHandler->getString(
            element: $entry,
            field: 'heroHeading',
        );

        $heroHeadingSubheading = $this->genericHandler->getString(
            element: $entry,
            field: 'heroSubheading',
        );

        $heroParagraph = $this->genericHandler->getString(
            element: $entry,
            field: 'heroParagraph',
        );

        $useShortHero = $this->genericHandler->getBoolean(
            element: $entry,
            field: 'useShortHero',
        );

        $hasHero = $heroImageAsset !== null;

        return new Hero(
            heroOverlayOpacity: $hasHero ?
                $this->genericHandler->getInt(
                    element: $entry,
                    field: 'heroDarkeningOverlayOpacity',
                ) :
                $this->defaultOverlayOpacity->getDefaultHeroOverlayOpacity(),
            heroImageUrl: $hasHero ?
                (string) $heroImageAsset->getUrl() :
                $this->defaultImageUrl->getDefaultHeroImageUrl(),
            upperCta: $this->linkFactory->fromLinkFieldModel(
                linkFieldModel: $heroUpperCta,
            ),
            heroHeading: $heroHeading,
            heroSubHeading: $heroHeadingSubheading,
            heroParagraph: $heroParagraph,
            useShortHero: $useShortHero,
        );
    }
}

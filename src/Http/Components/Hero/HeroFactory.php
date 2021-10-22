<?php

declare(strict_types=1);

namespace App\Http\Components\Hero;

use App\Http\Components\Link\LinkFactory;
use App\Templating\TwigExtensions\HeroImageUrl\GetDefaultHeroImageUrl;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use typedlinkfield\models\Link;
use yii\base\InvalidConfigException;

use function assert;

class HeroFactory
{
    public function __construct(
        private LinkFactory $linkFactory,
        private GetDefaultHeroImageUrl $getDefaultHeroImageUrl,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function createFromEntry(Entry $entry): Hero
    {
        $heroImageQuery = $entry->getFieldValue('heroImage');

        assert($heroImageQuery instanceof AssetQuery);

        $heroImageAsset = $heroImageQuery->one();

        assert(
            $heroImageAsset instanceof Asset ||
            $heroImageAsset === null,
        );

        $heroUpperCta = $entry->getFieldValue('heroUpperCta');

        assert($heroUpperCta instanceof Link);

        $heroHeading = (string) $entry->getFieldValue(
            'heroHeading',
        );

        $heroHeadingSubheading = (string) $entry->getFieldValue(
            'heroSubheading',
        );

        $heroParagraph = (string) $entry->getFieldValue(
            'heroParagraph',
        );

        $useShortHero = (bool) $entry->getFieldValue(
            'useShortHero',
        );

        return new Hero(
            heroImageUrl: $heroImageAsset !== null ?
                (string) $heroImageAsset->getUrl() :
                $this->getDefaultHeroImageUrl->getDefaultHeroImageUrl(),
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

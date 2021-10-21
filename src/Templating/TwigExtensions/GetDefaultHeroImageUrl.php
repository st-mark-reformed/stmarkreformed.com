<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions;

use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\GlobalSet;
use craft\errors\InvalidFieldException;
use craft\services\Globals;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use yii\base\InvalidConfigException;

use function assert;

class GetDefaultHeroImageUrl extends AbstractExtension
{
    public function __construct(private Globals $globals)
    {
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [$this->getFunction()];
    }

    private function getFunction(): TwigFunction
    {
        return new TwigFunction(
            'getDefaultHeroImageUrl',
            [$this, 'getDefaultHeroImageUrl']
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function getDefaultHeroImageUrl(): string
    {
        $generalSet = $this->globals->getSetByHandle(
            'general',
        );

        assert($generalSet instanceof GlobalSet);

        $assetQuery = $generalSet->getFieldValue(
            'defaultHeroImage',
        );

        assert($assetQuery instanceof AssetQuery);

        $asset = $assetQuery->one();

        assert($asset instanceof Asset);

        return (string) $asset->getUrl();
    }
}

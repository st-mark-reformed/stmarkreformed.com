<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\HeroImage;

use craft\elements\GlobalSet;
use craft\errors\InvalidFieldException;
use craft\services\Globals;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function assert;

class GetDefaultHeroOverlayOpacity extends AbstractExtension
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
            'getDefaultHeroOverlayOpacity',
            [$this, 'getDefaultHeroOverlayOpacity']
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function getDefaultHeroOverlayOpacity(): int
    {
        $generalSet = $this->globals->getSetByHandle(
            'general',
        );

        assert($generalSet instanceof GlobalSet);

        return (int) $generalSet->getFieldValue(
            'heroDarkeningOverlayOpacity',
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Components\Hero;

use App\Http\Components\Link\Link;

class Hero
{
    public function __construct(
        private string $heroImageUrl,
        private Link $upperCta,
        private string $heroHeading,
        private string $heroSubHeading = '',
        private string $heroParagraph = '',
        private bool $useShortHero = true,
    ) {
    }

    public function heroImageUrl(): string
    {
        return $this->heroImageUrl;
    }

    public function upperCta(): Link
    {
        return $this->upperCta;
    }

    public function heroHeading(): string
    {
        return $this->heroHeading;
    }

    public function heroSubHeading(): string
    {
        return $this->heroSubHeading;
    }

    public function heroParagraph(): string
    {
        return $this->heroParagraph;
    }

    public function useShortHero(): bool
    {
        return $this->useShortHero;
    }
}

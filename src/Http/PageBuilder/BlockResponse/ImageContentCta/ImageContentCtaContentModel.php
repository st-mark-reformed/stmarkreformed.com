<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageContentCta;

use App\Http\Components\Link\Link;
use Twig\Markup;

class ImageContentCtaContentModel
{
    public function __construct(
        private string $tailwindBackgroundColor,
        private string $contentDisposition,
        private string $imageUrl,
        private string $imageAltText,
        private bool $showTealOverlayOnImage,
        private string $preHeadline,
        private string $headline,
        private Markup $content,
        private Link $cta,
    ) {
    }

    public function tailwindBackgroundColor(): string
    {
        return $this->tailwindBackgroundColor;
    }

    public function contentDisposition(): string
    {
        return $this->contentDisposition;
    }

    public function imageUrl(): string
    {
        return $this->imageUrl;
    }

    public function imageAltText(): string
    {
        return $this->imageAltText;
    }

    public function showTealOverlayOnImage(): bool
    {
        return $this->showTealOverlayOnImage;
    }

    public function preHeadline(): string
    {
        return $this->preHeadline;
    }

    public function headline(): string
    {
        return $this->headline;
    }

    public function content(): Markup
    {
        return $this->content;
    }

    public function cta(): Link
    {
        return $this->cta;
    }
}

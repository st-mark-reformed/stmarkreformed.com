<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Gallery;

use craft\elements\Asset;
use yii\base\InvalidConfigException;

class GalleryItem
{
    /**
     * @throws InvalidConfigException
     *
     * @phpstan-ignore-next-line
     */
    public static function fromAsset(Asset $asset): self
    {
        return new self(
            imgUrl: (string) $asset->getUrl(),
            title: (string) $asset->title,
        );
    }

    public function __construct(
        private string $imgUrl,
        private string $title,
    ) {
    }

    public function imgUrl(): string
    {
        return $this->imgUrl;
    }

    public function title(): string
    {
        return $this->title;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resource;

use craft\elements\Asset;
use yii\base\InvalidConfigException;

class ResourceDownloadItem
{
    /**
     * @throws InvalidConfigException
     *
     * @phpstan-ignore-next-line
     */
    public static function fromAsset(Asset $asset): self
    {
        return new self(
            url: (string) $asset->getUrl(),
            filename: $asset->getFilename(),
        );
    }

    public function __construct(
        private string $url,
        private string $filename,
    ) {
    }

    public function url(): string
    {
        return $this->url;
    }

    public function filename(): string
    {
        return $this->filename;
    }
}

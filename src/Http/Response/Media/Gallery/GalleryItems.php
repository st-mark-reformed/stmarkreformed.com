<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Gallery;

use craft\elements\Asset;
use yii\base\InvalidConfigException;

use function array_map;

class GalleryItems
{
    /**
     * @param Asset[] $assets
     *
     * @throws InvalidConfigException
     */
    public static function fromAssets(array $assets): self
    {
        return new self(array_map(
            static fn (Asset $asset) => GalleryItem::fromAsset(
                asset: $asset,
            ),
            $assets,
        ));
    }

    /** @var GalleryItem[] */
    private array $items;

    /**
     * @param GalleryItem[] $items
     */
    public function __construct(array $items)
    {
        array_map(
            function (GalleryItem $item): void {
                $this->items[] = $item;
            },
            $items,
        );
    }

    /**
     * @return GalleryItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return mixed[]
     */
    public function map(callable $callable): array
    {
        return array_map($callable, $this->items());
    }
}

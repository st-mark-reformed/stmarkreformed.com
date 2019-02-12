<?php

namespace dev\jobs;

use Craft;
use craft\queue\BaseJob;
use aelvan\imager\Imager;

class RunAssetTransformJob extends BaseJob
{
    public $description = 'Run Asset transform';

    public $assetId;

    /**
     * @throws \Throwable
     */
    public function execute($queue)
    {
        $asset = Craft::$app->getAssets()->getAssetById($this->assetId);

        if (! $asset) {
            return;
        }

        // If this is not an image we can stop here
        if (! $asset->getHeight()) {
            return;
        }

        $imager = Imager::$plugin->imager;

        $imager->transformImage($asset, [
            'height' => 67,
            'width' => 100,
            'position' => $asset->getFocalPoint(),
        ]);

        $imager->transformImage($asset, [
            'height' => 134,
            'width' => 200,
            'position' => $asset->getFocalPoint(),
        ]);

        $imager->transformImage($asset, [
            'width' => 120,
        ]);

        $imager->transformImage($asset, [
            'width' => 240,
        ]);

        $imager->transformImage($asset, [
            'width' => 300,
        ]);

        $image400Width = min(400, (int) $asset->width);
        $imager->transformImage($asset, [
            'width' => $image400Width,
        ]);
        $imager->transformImage($asset, [
            'width' => $image400Width * 2,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 400,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 500,
        ]);

        $imager->transformImage($asset, [
            'width' => 600,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 700,
        ]);

        $image800Width = min(800, (int) $asset->width);
        $imager->transformImage($asset, [
            'width' => $image800Width,
        ]);
        $imager->transformImage($asset, [
            'width' => $image800Width * 2,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 800,
        ]);

        $imager->transformImage($asset, [
            'width' => min(900, (int) $asset->width),
        ]);

        $imager->transformImage($asset, [
            'width' => min(900, (int) $asset->width) * 2,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1000,
        ]);

        $image1200Width = min(1200, (int) $asset->width);
        $imager->transformImage($asset, [
            'width' => $image1200Width,
        ]);
        $imager->transformImage($asset, [
            'width' => $image1200Width * 2,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1200,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1400,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1600,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1800,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1920,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 2400,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 2800,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 3600,
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 3840,
        ]);
    }
}

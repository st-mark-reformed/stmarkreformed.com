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
            'allowUpscale' => false,
            'width' => 500
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 800
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1000
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1200
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1600
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 1920
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 2400
        ]);

        $imager->transformImage($asset, [
            'allowUpscale' => false,
            'width' => 3840
        ]);
    }
}

<?php

namespace dev\services;

use Craft;
use dev\jobs\RunAssetTransformJob;

class InitAssetTransformJobService
{
    public function init(int $assetId)
    {
        Craft::$app->getQueue()->push(new RunAssetTransformJob([
            'assetId' => $assetId,
        ]));
    }
}

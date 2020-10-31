<?php

namespace src\services;

use Craft;
use src\jobs\RunAssetTransformJob;

class InitAssetTransformJobService
{
    public function init(int $assetId)
    {
        Craft::$app->getQueue()->push(new RunAssetTransformJob([
            'assetId' => $assetId,
        ]));
    }
}

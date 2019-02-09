<?php

namespace dev\jobs;

use craft\queue\BaseJob;
use craft\elements\Asset;

class InitAllAssetsTransformsJob extends BaseJob
{
    public $description = 'Init all Assets transforms';

    public function execute($queue)
    {
        foreach (Asset::findAll() as $asset) {
            if (! $asset->getHeight()) {
                return;
            }

            $queue->push(new RunAssetTransformJob([
                'assetId' => (int) $asset->id,
            ]));
        }

        sleep(1);
    }
}

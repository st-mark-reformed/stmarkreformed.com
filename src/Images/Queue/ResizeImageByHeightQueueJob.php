<?php

declare(strict_types=1);

namespace App\Images\Queue;

use App\Images\Resize\ResizeByHeight\ResizeByHeight;
use Config\di\Container;
use craft\queue\BaseJob;

use function assert;

/**
 * @codeCoverageIgnore
 */
class ResizeImageByHeightQueueJob extends BaseJob
{
    public function __construct(
        private string $pathOrUrl,
        private int $pixelHeight,
    ) {
        parent::__construct();
    }

    /**
     * Returns the default description for this job.
     */
    protected function defaultDescription(): string
    {
        return 'Resize image by height (' . $this->pixelHeight . '): ' .
            $this->pathOrUrl;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue): void
    {
        $resizeByWidth = Container::get()->get(ResizeByHeight::class);

        assert($resizeByWidth instanceof ResizeByHeight);

        $resizeByWidth->resize(
            pathOrUrl: $this->pathOrUrl,
            pixelHeight: $this->pixelHeight,
        );
    }
}

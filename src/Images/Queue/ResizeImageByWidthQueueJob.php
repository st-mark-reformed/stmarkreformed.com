<?php

declare(strict_types=1);

namespace App\Images\Queue;

use App\Images\Resize\ResizeByWidth\ResizeByWidth;
use Config\di\Container;
use craft\queue\BaseJob;

use function assert;

/**
 * @codeCoverageIgnore
 */
class ResizeImageByWidthQueueJob extends BaseJob
{
    public function __construct(
        private string $pathOrUrl,
        private int $pixelWidth,
    ) {
        parent::__construct();
    }

    /**
     * Returns the default description for this job.
     */
    protected function defaultDescription(): string
    {
        return 'Resize image by width (' . $this->pixelWidth . '): ' .
            $this->pathOrUrl;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue): void
    {
        $resizeByWidth = Container::get()->get(ResizeByWidth::class);

        assert($resizeByWidth instanceof ResizeByWidth);

        $resizeByWidth->resize(
            pathOrUrl: $this->pathOrUrl,
            pixelWidth: $this->pixelWidth,
        );
    }
}

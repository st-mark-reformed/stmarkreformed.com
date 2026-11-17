<?php

declare(strict_types=1);

namespace App\Queue;

use BuzzingPixel\Queue\QueueHandler;
use BuzzingPixel\Queue\QueueItemFailed;
use Psr\Cache\CacheItemPoolInterface;

use function array_map;
use function count;

/**
 * The queue library has no way to delete failed items. They're stored in the
 * same cache pool the app's CacheItemPoolInterface resolves to, so delete
 * them from there.
 */
readonly class FailedQueueItemClearer
{
    public function __construct(
        private QueueHandler $queueHandler,
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    public function clear(string $key): bool
    {
        // Only delete keys that hold a failed queue item, so this can't be
        // used to delete arbitrary cache entries
        if (! $this->queueHandler->findFailedItemByKey($key)->hasResult) {
            return false;
        }

        return $this->cachePool->deleteItem($key);
    }

    /** @return int The number of failed items cleared */
    public function clearAll(): int
    {
        $keys = array_map(
            static fn (QueueItemFailed $item) => $item->key,
            $this->queueHandler->getFailedItems()->items,
        );

        $this->cachePool->deleteItems($keys);

        return count($keys);
    }
}

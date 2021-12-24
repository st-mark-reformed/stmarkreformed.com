<?php

declare(strict_types=1);

namespace App\Shared\Entries;

use craft\elements\Entry;

class EntryHandler
{
    /**
     * @phpstan-ignore-next-line
     */
    public function getRootEntry(Entry $entry): Entry
    {
        $parent = $entry->getParent();

        if ($parent === null) {
            return $entry;
        }

        return $this->getRootEntry(entry: $parent);
    }
}

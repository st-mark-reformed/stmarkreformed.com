<?php

declare(strict_types=1);

namespace App\Shared\ElementQueryFactories;

use craft\elements\db\EntryQuery;
use craft\elements\Entry;

/**
 * @codeCoverageIgnore
 */
class EntryQueryFactory
{
    /**
     * @phpstan-ignore-next-line
     */
    public function make(): EntryQuery
    {
        return Entry::find();
    }
}

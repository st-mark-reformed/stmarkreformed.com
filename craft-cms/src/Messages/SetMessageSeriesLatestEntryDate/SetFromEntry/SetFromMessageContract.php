<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate\SetFromEntry;

use craft\elements\Category;
use craft\elements\Entry;

interface SetFromMessageContract
{
    /**
     * @phpstan-ignore-next-line
     */
    public function set(Category $series, ?Entry $message): void;
}

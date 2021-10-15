<?php

declare(strict_types=1);

namespace App\Craft\SetMessageEntrySlug\Services;

use Cocur\Slugify\Slugify;
use craft\elements\Entry;
use DateTime;

use function assert;

class SetMessageEntrySlug implements SetMessageEntrySlugContract
{
    public function __construct(private Entry $entry)
    {
    }

    public function set(): void
    {
        $postDate = $this->entry->postDate;

        assert($postDate instanceof DateTime);

        $date = $postDate->format('Y-m-d');

        $this->entry->slug = (new Slugify())->slugify(
            $date . '-' . (string) $this->entry->title,
        );
    }
}

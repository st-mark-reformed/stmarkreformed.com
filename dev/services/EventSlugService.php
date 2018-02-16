<?php

namespace dev\services;

use Craft;
use craft\elements\Entry;
use Cocur\Slugify\Slugify;

/**
 * Class EventSlugService
 */
class EventSlugService
{
    /**
     * Get config
     * @param Entry $entry
     * @throws \Exception
     */
    public function setEventEntrySlug(Entry $entry)
    {
        if ($entry->getSection()->handle !== 'events' ||
            ! $entry->getFieldValue('startDate')
        ) {
            return;
        }

        /** @var \DateTime $startDate */
        $startDate = $entry->getFieldValue('startDate');
        $date = $startDate->format('Y-m-d');
        $title = $entry->title;

        $entry->slug = (new Slugify())->slugify("{$date}-{$title}");
    }
}

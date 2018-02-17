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

        /** @var \DateTime $endDate */
        $endDate = $entry->getFieldValue('endDate');

        if (! $endDate ||
            $endDate->getTimestamp() < $startDate->getTimestamp()
        ) {
            $entry->setFieldValue('endDate', $startDate);
        }

        $entry->slug = (new Slugify())->slugify("{$date}-{$title}");
    }
}

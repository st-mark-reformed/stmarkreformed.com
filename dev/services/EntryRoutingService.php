<?php

namespace dev\services;

use craft\elements\Entry;
use craft\events\SetElementRouteEvent;

/**
 * Class EntryService
 */
class EntryRoutingService
{
    /**
     * Handles page entries
     * @param SetElementRouteEvent $eventModel
     * @throws \Exception
     */
    public function pageEntryRouteHandler(SetElementRouteEvent $eventModel)
    {
        /** @var Entry $entry */
        $entry = $eventModel->sender;

        if ($entry->getSection()->handle !== 'pages' ||
            $entry->getType()->handle !== 'navLink'
        ) {
            return;
        }

        $eventModel->route = '';
    }
}

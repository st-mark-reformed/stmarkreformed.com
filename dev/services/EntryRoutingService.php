<?php

namespace dev\services;

use craft\elements\Entry;
use craft\events\SetElementRouteEvent;
use craft\models\Section_SiteSettings;

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

    /**
     * Handles entry controller routing
     * @param SetElementRouteEvent $eventModel
     * @throws \Exception
     */
    public function entryControllerRouting(SetElementRouteEvent $eventModel)
    {
        /** @var Entry $entry */
        $entry = $eventModel->sender;

        /** @var Section_SiteSettings $sectionSiteSettingsModel */
        $sectionSiteSettingsModel = $entry->getSection()->getSiteSettings()[1];

        $routeArray = explode('/', $sectionSiteSettingsModel->template);

        if (! isset($routeArray[0]) || $routeArray[0] !== '_controllerRoute') {
            return;
        }

        unset($routeArray[0]);

        $route = implode('/', $routeArray);

        $eventModel->route = [$route, [
            'entry' => $entry
        ]];
    }
}

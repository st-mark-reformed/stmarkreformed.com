<?php

namespace src\services;

use craft\elements\Entry;
use craft\events\SetElementRouteEvent;
use craft\models\Section_SiteSettings;

/**
 * Class EntryService
 */
class EntryRoutingService
{
    /**
     * Handles entry controller routing
     * @param SetElementRouteEvent $eventModel
     * @throws \Exception
     */
    public function entryControllerRouting(SetElementRouteEvent $eventModel)
    {
        /** @var Entry $entry */
        $entry = $eventModel->sender;

        if ($entry->getType()->handle === 'navLink') {
            $eventModel->route = '';
            return;
        }

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

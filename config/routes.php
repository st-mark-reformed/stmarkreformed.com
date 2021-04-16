<?php

/**
 * Site URL Rules
 *
 * See http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html for more
 * info about URL rules.
 *
 * In addition to Yii’s supported syntaxes, Craft supports a shortcut syntax for
 * defining template routes:
 *
 *     'blog/archive/<year:\d{4}>' => ['template' => 'blog/_archive'],
 *
 * That example would match URIs such as `/blog/archive/2012`, and pass the
 * request along to the `blog/_archive` template, providing it a `year` variable
 * set to the value `2012`.
 */

return [

    /**************************************************************************/
    /* Messages routing */
    /**************************************************************************/

    /**
     * Pages routing set by Pages section in Craft
     *
     * @see \src\services\EntryRoutingService
     * @see \src\controllers\PagesController::actionPage()
     */
    // '{parent.uri}/{slug}' => 'dev/pages/page',





    /**************************************************************************/
    /* Messages routing */
    /**************************************************************************/

    /**
     * Messages feeds routes
     */
    'media/<section:messages>/feed' => ['template' => '_core/MessagesFeed.xml'],
    'sermons' => ['template' => '_core/MessagesNewFeedRedirect.xml'],

    /**
     * Messages listing routes
     *
     * @see \src\controllers\MessagesController::actionIndex()
     */
    'media/messages' => 'dev/messages/index',
    'media/messages/page/<pageNum:\d+>' => 'dev/messages/index',
    'media/messages/<filter>' => 'dev/messages/index',
    'media/messages/<filter>/page/<pageNum:\d+>' => 'dev/messages/index',

    /**
     * Messages speakers listing routes
     *
     * @see \src\controllers\MessagesController::actionIndex()
     */
    'media/messages/by/<speaker:([^\/]+)>' => 'dev/messages/index',
    'media/messages/by/<speaker:([^\/]+)>/page/<pageNum:\d+>' => 'dev/messages/index',

    /**
     * Messages series listing routes
     *
     * @see \src\controllers\MessagesController::actionIndex()
     */
    'media/messages/series/<series:([^\/]+)>' => 'dev/messages/index',
    'media/messages/series/<series:([^\/]+)>/page/<pageNum:\d+>' => 'dev/messages/index',

    /**
     * Messages single entry pages set by Messages section in Craft
     *
     * @see \src\services\EntryRoutingService
     * @see \src\controllers\MessagesController::actionEntry()
     */
    // 'media/messages/{slug}' => 'dev/messages/entry',





    /**************************************************************************/
    /* Galleries routing */
    /**************************************************************************/

    /**
     * Galleries listing routes
     *
     * @see \src\controllers\GalleriesController::actionIndex()
     */
    'media/galleries' => 'dev/galleries/index',
    'media/galleries/page/<pageNum:\d+>' => 'dev/galleries/index',

    /**
     * Galleries single entry pages set by Galleries section in Craft
     *
     * @see \src\services\EntryRoutingService
     * @see \src\controllers\GalleriesController::actionEntry()
     */
    // 'galleries/{slug}' => 'dev/galleries/entry',





    /**************************************************************************/
    /* Events routing */
    /**************************************************************************/

    /**
     * Events listing routes
     * @see \src\controllers\EventsController::actionIndex()
     */
    'events' => 'dev/events/index',
    'events/page/<pageNum:\d+>' => 'dev/events/index',

    /**
     * Events single entry pages set by Events section in Craft
     *
     * @see \src\services\EntryRoutingService
     * @see \src\controllers\EventsController::actionEntry()
     */
    // 'events/{slug}' => 'dev/events/entry',





    /**************************************************************************/
    /* News routing */
    /**************************************************************************/

    /**
     * News listing routes
     * @see \src\controllers\NewsController::actionIndex()
     */
    '<section:news>' => 'dev/news/index',
    '<section:news>/page/<pageNum:\d+>' => 'dev/news/index',

    /**
     * News single entry pages set by News section in Craft
     *
     * @see \src\services\EntryRoutingService
     * @see \src\controllers\NewsController::actionEntry()
     */
    // 'news/{slug}' => 'dev/news/entry',





    /**************************************************************************/
    /* Resources routing */
    /**************************************************************************/

    /**
     * News listing routes
     * @see \src\controllers\ResourcesController::actionIndex()
     */
    'resources' => 'dev/resources/index',
    'resources/page/<pageNum:\d+>' => 'dev/resources/index',

    /**
     * News single entry pages set by News section in Craft
     *
     * @see \src\services\EntryRoutingService
     * @see \src\controllers\ResourcesController::actionEntry()
     */
    // 'resources/{slug}' => 'dev/resources/entry',





    /**************************************************************************/
    /* Pastor's Page routing */
    /**************************************************************************/

    /**
     * Pastor's Page listing routes
     * @see \src\controllers\NewsController::actionIndex()
     */
    '<section:pastors-page>' => 'dev/news/index',
    '<section:pastors-page>/page/<pageNum:\d+>' => 'dev/news/index',

    /**
     * Pastor's Page single entry pages set by Pastor's Page section in Craft
     *
     * @see \src\services\EntryRoutingService
     * @see \src\controllers\NewsController::actionEntry()
     */
    // 'pastors-page/{slug}' => 'dev/news/entry',





    /**************************************************************************/
    /* Site map routing */
    /**************************************************************************/

    /**
     * Site map index
     * @see \src\controllers\SiteMapController::actionIndex()
     */
    'sitemap' => 'dev/site-map/index',

    /**
     * Site map pages
     * @see \src\controllers\SiteMapController::actionPages()
     */
    'sitemap/pages' => 'dev/site-map/pages',

    /**
     * Site map events
     * @see \src\controllers\SiteMapController::actionEvents()
     */
    'sitemap/events' => 'dev/site-map/events',

    /**
     * Site map galleries
     * @see \src\controllers\SiteMapController::actionGalleries()
     */
    'sitemap/galleries' => 'dev/site-map/galleries',

    /**
     * Site map messages
     * @see \src\controllers\SiteMapController::actionMessages()
     */
    'sitemap/messages' => 'dev/site-map/messages',

    /**
     * Site map news
     * @see \src\controllers\SiteMapController::actionNews()
     */
    'sitemap/news' => 'dev/site-map/news',

    /**
     * Site map news
     * @see \src\controllers\SiteMapController::actionPastorsPage()
     */
    'sitemap/pastors-page' => 'dev/site-map/pastors-page',

    /**
     * Members area
     */

    /** @see \src\controllers\MembersController::actionIndex */
    'members' => 'dev/members/index',

    /** @see \src\controllers\MembersLogInController::actionLogIn */
    'members/log-in' => 'dev/members-log-in/log-in',

    /** @see \src\controllers\MembersHymnsOfTheMonthController */
    'members/hymns-of-the-month' => 'dev/members-hymns-of-the-month/index',
    'members/hymns-of-the-month/page/<pageNum:\d+>' => 'dev/members-hymns-of-the-month/index',

    /**
     * @see \src\services\EntryRoutingService
     * @see \src\controllers\MembersHymnsOfTheMonthController::actionEntry
     */
    // 'members/hymns-of-the-month/{slug}' => 'dev/members-hymns-of-the-month/entry',

    /** @see \src\controllers\MembersHymnsOfTheMonthController::actionDownloadFile */
    'members/hymns-of-the-month/<slug:([^\/]+)>/<folder:([^\/]+)>/<fileName:([^\/]+)>' => 'dev/members-hymns-of-the-month/download-file',

];

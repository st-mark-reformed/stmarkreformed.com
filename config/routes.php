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
     * @see \dev\services\EntryRoutingService
     * @see \dev\controllers\PagesController::actionPage()
     */
    // '{parent.uri}/{slug}' => 'dev/pages/page',





    /**************************************************************************/
    /* Messages routing */
    /**************************************************************************/

    /**
     * Messages listing routes
     * @see \dev\controllers\MessagesController::actionIndex()
     */
    'GET media/messages' => 'dev/messages/index',
    'GET media/messages/page/<pageNum:\d+>' => 'dev/messages/index',

    /**
     * Messages speakers listing routes
     * @see \dev\controllers\MessagesController::actionIndex()
     */
    'GET media/messages/by/<speaker:([^\/]+)>' => 'dev/messages/index',
    'GET media/messages/by/<speaker:([^\/]+)>/page/<pageNum:\d+>' => 'dev/messages/index',

    /**
     * Messages series listing routes
     * @see \dev\controllers\MessagesController::actionIndex()
     */
    'GET media/messages/series/<series:([^\/]+)>' => 'dev/messages/index',
    'GET media/messages/series/<series:([^\/]+)>/page/<pageNum:\d+>' => 'dev/messages/index',

    /**
     * Messages feeds routes
     */
    'GET media/<section:messages>/feed' => ['template' => '_core/MessagesFeed.xml'],
    'GET sermons' => ['template' => '_audio/MessagesNewFeedRedirect.xml'],

    /**
     * Messages single entry pages set by Messages section in Craft
     * @see \dev\services\EntryRoutingService
     * @see \dev\controllers\MessagesController::actionEntry()
     */
    // 'media/messages/{slug}' => 'dev/messages/entry',





    /**************************************************************************/
    /* Galleries routing */
    /**************************************************************************/

    /**
     * Galleries listing routes
     * @see \dev\controllers\GalleriesController::actionIndex()
     */
    'GET media/galleries' => 'dev/galleries/index',
    'GET media/galleries/page/<pageNum:\d+>' => 'dev/galleries/index',

    /**
     * Galleries single entry pages set by Galleries section in Craft
     * @see \dev\services\EntryRoutingService
     * @see \dev\controllers\MessagesController::actionEntry()
     */
    // 'galleries/{slug}' => 'dev/galleries/entry',





    /**************************************************************************/
    /* Events routing */
    /**************************************************************************/

    /**
     * Events listing routes
     * @see \dev\controllers\EventsController::actionIndex()
     */
    'GET events' => 'dev/events/index',
    'GET events/page/<pageNum:\d+>' => 'dev/events/index',

    /**
     * Events single entry pages set by Events section in Craft
     * @see \dev\services\EntryRoutingService
     * @see \dev\controllers\EventsController::actionEntry()
     */
    // 'events/{slug}' => 'dev/events/entry',





    /**************************************************************************/
    /* News routing */
    /**************************************************************************/

    /**
     * News listing routes
     * @see \dev\controllers\NewsController::actionIndex()
     */
    'GET <section:news>' => 'dev/news/index',
    'GET <section:news>/page/<pageNum:\d+>' => 'dev/news/index',

    /**
     * News single entry pages set by News section in Craft
     * @see \dev\services\EntryRoutingService
     * @see \dev\controllers\NewsController::actionEntry()
     */
    // 'news/{slug}' => 'dev/news/entry',





    /**************************************************************************/
    /* Pastor's Page routing */
    /**************************************************************************/

    /**
     * Pastor's Page listing routes
     * @see \dev\controllers\NewsController::actionIndex()
     */
    'GET <section:pastors-page>' => 'dev/news/index',
    'GET <section:pastors-page>/page/<pageNum:\d+>' => 'dev/news/index',

    /**
     * Pastor's Page single entry pages set by Pastor's Page section in Craft
     * @see \dev\services\EntryRoutingService
     * @see \dev\controllers\NewsController::actionEntry()
     */
    // 'pastors-page/{slug}' => 'dev/news/entry',

];

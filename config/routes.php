<?php
/**
 * Site URL Rules
 *
 * You can define custom site URL rules here, which Craft will check in addition
 * to any routes you’ve defined in Settings → Routes.
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

// Useful route regexes
// $routeRegex = array(
//     'any' => '([^\/]+)',
//     'all' => '(.*)?',
//     'num' => '(\d+)',
//     'year' => '(\d{4})',
//     'month' => '(\d{2})',
//     'day' => '(\d{2})'
// );

return [
    /**
     * Sermons
     */

    // Sermons index
    'media/<section:messages>' => ['template' => '_audio/index'],
    'media/<section:messages>/page/<pageNum:\d+>' => ['template' => '_audio/index'],

    // Speakers
    'media/<section:messages>/by/<speaker:([^\/]+)>' => ['template' => '_audio/index'],
    'media/<section:messages>/by/<speaker:([^\/]+)>/page/<pageNum:\d+>' => ['template' => '_audio/index'],

    // Series
    'media/<section:messages>/series/<series:([^\/]+)>' => ['template' => '_audio/index'],
    'media/<section:messages>/series/<series:([^\/]+)>/page/<pageNum:\d+>' => ['template' => '_audio/index'],

    // Sermons feed
    'media/<section:messages>/feed' => ['template' => '_audio/feed.xml'],
    'sermons' => ['template' => '_audio/newFeedRedirect.xml'],


    /**
     * Galleries
     */

    'media/galleries' => ['template' => '_galleries/index'],
    'media/galleries/page/<pageNum:\d+>' => ['template' => '_galleries/index'],
];

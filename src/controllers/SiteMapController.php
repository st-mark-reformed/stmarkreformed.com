<?php

namespace src\controllers;

use Craft;
use craft\elements\User;
use craft\elements\Entry;
use craft\elements\Category;

/**
 * Class PagesController
 */
class SiteMapController extends BaseController
{
    /** @var string $siteUrl */
    private $siteUrl;

    /** @var string $siteMapBase */
    private $siteMapBase;

    /**
     * Initialization
     * @throws \Exception
     */
    public function init()
    {
        $this->siteUrl = rtrim(
            Craft::$app->getSites()->getCurrentSite()->baseUrl,
            '/'
        );

        $this->siteMapBase = "{$this->siteUrl}/sitemap";
    }

    /**
     * Displays the site map index page
     * @throws \Exception
     */
    public function actionIndex()
    {
        return $this->renderTemplate(
            '_core/SiteMap.xml',
            [
                'siteMapIndex' => [
                    [
                        'loc' => "{$this->siteMapBase}/pages",
                    ],
                    [
                        'loc' => "{$this->siteMapBase}/events",
                    ],
                    [
                        'loc' => "{$this->siteMapBase}/galleries",
                    ],
                    [
                        'loc' => "{$this->siteMapBase}/messages",
                    ],
                    [
                        'loc' => "{$this->siteMapBase}/news",
                    ],
                    [
                        'loc' => "{$this->siteMapBase}/pastors-page",
                    ],
                ],
            ],
            false,
            false
        );
    }

    /**
     * Displays pages site map
     * @throws \Exception
     */
    public function actionPages()
    {
        $entries = Entry::find()->section('pages')
            ->searchEngineIndexing(true)
            ->all();

        $urlSet = [];

        foreach ($entries as $entry) {
            if ($entry->getType()->handle !== 'page' ||
                $entry->slug === '__404__'
            ) {
                continue;
            }

            $urlSet[] = [
                'loc' => $entry->getUrl(),
                'lastmod' => $entry->dateUpdated,
                'changefreq' => 'weekly',
            ];
        }

        return $this->renderTemplate(
            '_core/SiteMap.xml',
            [
                'siteMapUrlSet' => $urlSet,
            ],
            false,
            false
        );
    }

    /**
     * Displays events site map
     * @throws \Exception
     */
    public function actionEvents()
    {
        $dayAfter = new \DateTime();
        $dayAfter->setTimestamp(strtotime('-1 day'));
        $dayAfter = $dayAfter->format('Y-m-d G:i:s');

        $entries = Entry::find()->section('events')
            ->searchEngineIndexing(true)
            ->endDate("> {$dayAfter}")
            ->all();

        $urlSet = [[
            'loc' => "{$this->siteUrl}/events",
            'lastmod' => $entries[0]->dateUpdated,
            'changefreq' => 'weekly',
        ]];

        foreach ($entries as $entry) {
            $urlSet[] = [
                'loc' => $entry->getUrl(),
                'lastmod' => $entry->dateUpdated,
                'changefreq' => 'weekly',
            ];
        }

        return $this->renderTemplate(
            '_core/SiteMap.xml',
            [
                'siteMapUrlSet' => $urlSet,
            ],
            false,
            false
        );
    }

    /**
     * Displays galleries site map
     * @throws \Exception
     */
    public function actionGalleries()
    {
        $entries = Entry::find()->section('galleries')
            ->searchEngineIndexing(true)
            ->all();

        $urlSet = [[
            'loc' => "{$this->siteUrl}/galleries",
            'lastmod' => $entries[0]->dateUpdated,
            'changefreq' => 'weekly',
        ]];

        foreach ($entries as $entry) {
            $urlSet[] = [
                'loc' => $entry->getUrl(),
                'lastmod' => $entry->dateUpdated,
                'changefreq' => 'weekly',
            ];
        }

        return $this->renderTemplate(
            '_core/SiteMap.xml',
            [
                'siteMapUrlSet' => $urlSet,
            ],
            false,
            false
        );
    }

    /**
     * Displays messages site map
     * @throws \Exception
     */
    public function actionMessages()
    {
        $entries = Entry::find()->section('messages')
            ->searchEngineIndexing(true)
            ->all();

        $urlSet = [[
            'loc' => "{$this->siteUrl}/messages",
            'lastmod' => $entries[0]->dateUpdated,
            'changefreq' => 'weekly',
        ]];

        foreach ($entries as $entry) {
            $urlSet[] = [
                'loc' => $entry->getUrl(),
                'lastmod' => $entry->dateUpdated,
                'changefreq' => 'weekly',
            ];
        }

        $categories = Category::find()->group('messageSeries')
            ->orderBy('title asc')
            ->all();

        foreach ($categories as $category) {
            $urlSet[] = [
                'loc' => "{$this->siteUrl}/media/messages/series/{$category->slug}",
                'changefreq' => 'weekly',
            ];
        }

        $users = User::find()->group('speakers')
            ->orderBy('lastname asc')
            ->all();

        foreach ($users as $user) {
            $urlSet[] = [
                'loc' => "{$this->siteUrl}/media/messages/by/{$user->slugField}",
                'changefreq' => 'weekly',
            ];
        }

        return $this->renderTemplate(
            '_core/SiteMap.xml',
            [
                'siteMapUrlSet' => $urlSet,
            ],
            false,
            false
        );
    }

    /**
     * Displays news site map
     * @throws \Exception
     */
    public function actionNews()
    {
        $entries = Entry::find()->section('news')
            ->searchEngineIndexing(true)
            ->all();

        $urlSet = [[
            'loc' => "{$this->siteUrl}/news",
            'lastmod' => $entries[0]->dateUpdated,
            'changefreq' => 'weekly',
        ]];

        foreach ($entries as $entry) {
            $urlSet[] = [
                'loc' => $entry->getUrl(),
                'lastmod' => $entry->dateUpdated,
                'changefreq' => 'weekly',
            ];
        }

        return $this->renderTemplate(
            '_core/SiteMap.xml',
            [
                'siteMapUrlSet' => $urlSet,
            ],
            false,
            false
        );
    }

    /**
     * Displays pastors page site map
     * @throws \Exception
     */
    public function actionPastorsPage()
    {
        $entries = Entry::find()->section('pastorsPage')
            ->searchEngineIndexing(true)
            ->all();

        $urlSet = [[
            'loc' => "{$this->siteUrl}/pastors-page",
            'lastmod' => $entries[0]->dateUpdated,
            'changefreq' => 'weekly',
        ]];

        foreach ($entries as $entry) {
            $urlSet[] = [
                'loc' => $entry->getUrl(),
                'lastmod' => $entry->dateUpdated,
                'changefreq' => 'weekly',
            ];
        }

        return $this->renderTemplate(
            '_core/SiteMap.xml',
            [
                'siteMapUrlSet' => $urlSet,
            ],
            false,
            false
        );
    }
}

<?php

namespace dev\services;

use Craft;
use craft\base\Element;
use craft\elements\User;
use craft\helpers\Assets;
use craft\elements\Asset;
use craft\elements\Entry;
use Cocur\Slugify\Slugify;
use craft\elements\Category;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Class SermonImporterService
 */
class SermonImporterService
{
    /** @var string $trackingFilePath */
    private $trackingFilePath;

    /** @var array $importedAudio */
    private $importedAudio;

    /** @var string $audioDownloadPath */
    private $audioDownloadPath;

    /**
     * SermonImporterService constructor
     */
    public function __construct()
    {
        $this->trackingFilePath = \dirname(__DIR__) .
            '/sermonbatchtracking/tracking.json';

        if (! file_exists($this->trackingFilePath)) {
            file_put_contents($this->trackingFilePath, '[]');
        }

        $this->importedAudio = json_decode(
            file_get_contents($this->trackingFilePath)
        );

        $this->audioDownloadPath = \dirname(__DIR__) . '/sermonaudiodownload';
    }

    /**
     * Scrapes the DOM for sermons
     * @param string $url
     * @param string $baseUrl
     * @param string $batchDirPath
     * @param bool $firstPageOnly
     * @throws \Exception
     */
    public function scrapeDomForSermons(
        string $url,
        string $baseUrl,
        string $batchDirPath,
        bool $firstPageOnly = true
    ) {
        $batchDirPath = rtrim($batchDirPath, '/');
        $guzzleClient = new GuzzleClient();

        $page = $guzzleClient->request('GET', $url);

        $domDoc = new \DOMDocument();

        @$domDoc->loadHTML($page->getBody()->getContents());

        foreach ($domDoc->getElementsByTagName('table') as $table) {
            /** @var \DOMElement $table */
            if ($table->getAttribute('class') !== 'sermons') {
                continue;
            }

            $title = null;
            $passage = null;
            $series = null;
            $audioUrl = null;
            $speaker = null;
            $date = null;

            foreach ($table->getElementsByTagName('tr') as $tr) {
                /** @var \DOMElement $tr */
                foreach ($tr->getElementsByTagName('td') as $td) {
                    /** @var \DOMElement $td */
                    $class = $td->getAttribute('class');

                    switch ($class) {
                        case 'sermon-title':
                            $title = null;
                            $passage = null;
                            $series = null;
                            $audioUrl = null;
                            $speaker = null;
                            $date = null;

                            $a = $td->getElementsByTagName('a');

                            foreach ($a as $anchor) {
                                /** @var \DOMElement $anchor */
                                $title = $anchor->textContent;
                            }
                            break;
                        case 'sermon-passage':
                            $passageParts = explode('(', $td->textContent);
                            $passage = trim($passageParts[0]);
                            $passageParts = explode('Part of the ', $passageParts[1]);
                            $passageParts = explode(' series).', $passageParts[1]);
                            $series = trim($passageParts[0]);
                            break;
                        case 'files':
                            $sourceList = $td->getElementsByTagName('source');
                            foreach ($sourceList as $source) {
                                /** @var \DOMElement $source */
                                $audioUrl = $source->getAttribute('src');
                            }
                            break;
                        case 'preacher':
                            $parts = explode('Preached by ', $td->textContent);
                            $parts = explode(' on ', $parts[1]);
                            $speaker = $parts[0];
                            $parts = explode(' (', $parts[1]);
                            $date = $parts[0];

                            file_put_contents(
                                $batchDirPath . '/' . microtime(true) . '.json',
                                json_encode(compact(
                                    'title',
                                    'passage',
                                    'series',
                                    'audioUrl',
                                    'speaker',
                                    'date'
                                ))
                            );
                    }
                }
            }
        }

        if ($firstPageOnly) {
            return;
        }

        $nextPageUrl = null;

        foreach ($domDoc->getElementsByTagName('a') as $anchor) {
            /** @var \DOMElement $anchor */

            if ($anchor->textContent !== 'Next page »') {
                continue;
            }

            $nextPageUrl = "{$baseUrl}{$anchor->getAttribute('href')}";
        }

        if (! $nextPageUrl) {
            return;
        }

        $this->scrapeDomForSermons(
            $nextPageUrl,
            $baseUrl,
            $batchDirPath,
            false
        );
    }

    /**
     * Imports a sermon from json file path
     * @param string $filePath
     * @throws \Exception
     * @throws \Throwable if reasons
     */
    public function importSermonFromJsonFile(string $filePath)
    {
        $sermonJson = json_decode(file_get_contents($filePath));

        if (\in_array($sermonJson->audioUrl, $this->importedAudio, true)) {
            unlink($filePath);
            return;
        }

        parse_str($sermonJson->audioUrl, $query);

        if (isset($query['url'])) {
            $audioFileName = basename($query['url']);
        } elseif (isset($query['file_name'])) {
            $audioFileName = basename($query['file_name']);
        }

        $guzzleClient = new GuzzleClient();

        try {
            $guzzleClient->get($sermonJson->audioUrl, [
                'save_to' => "{$this->audioDownloadPath}/{$audioFileName}",
            ]);
        } catch (\Exception $e) {
            $this->addAudioTracking($sermonJson->audioUrl);
            unlink($filePath);
            return;
        }

        $assetsService = Craft::$app->getAssets();

        $folder = $assetsService->findFolder([
            'name' => 'Audio',
        ]);

        if ($folder === null) {
            throw new \Exception('reasons');
        }

        $assetFileName = Assets::prepareAssetName($audioFileName);

        $asset = new Asset();
        $asset->tempFilePath = "{$this->audioDownloadPath}/{$audioFileName}";
        $asset->filename = $assetFileName;
        $asset->newFolderId = $folder->id;
        $asset->volumeId = $folder->volumeId;
        $asset->avoidFilenameConflicts = true;
        $asset->setScenario(Asset::SCENARIO_CREATE);

        Craft::$app->getElements()->saveElement($asset);

        $section = Craft::$app->getSections()->getSectionByHandle('messages');

        if ($section === null) {
            throw new \Exception('reasons');
        }

        $userQuery = User::find()->importSearchString($sermonJson->speaker)
            ->one();

        $date = new \DateTime();
        $date->setTimestamp(strtotime("{$sermonJson->date} 11:00am"));

        $entry = new Entry();

        $entry->authorId = 1;
        $entry->sectionId = $section->id;
        $entry->typeId = $section->getEntryTypes()[0]->id;
        $entry->slug = (new Slugify())->slugify($sermonJson->title);
        $entry->postDate = $date;
        $entry->enabled = true;
        $entry->enabledForSite = true;
        $entry->title = $sermonJson->title;

        $entry->setFieldValue('audio', [$asset->id]);
        $entry->setFieldValue('speaker', [$userQuery->id]);
        $entry->setFieldValue('messageText', $sermonJson->passage);
        $entry->setFieldValue('messageSeries', [$this->getSeriesId($sermonJson->series)]);
        $entry->setFieldValue('shortDescription', '');
        $entry->setFieldValue('keywords', '');
        $entry->setFieldValue('searchEngineIndexing', '1');
        $entry->setFieldValue('seoTitle', '');
        $entry->setFieldValue('seoDescription', '');
        $entry->setFieldValue('customShareImage', '');

        Craft::$app->getElements()->saveElement($entry);

        $this->addAudioTracking($sermonJson->audioUrl);
        unlink($filePath);
    }

    /**
     * Adds audio tracking
     * @param $sermon
     */
    private function addAudioTracking($sermon)
    {
        $this->importedAudio[] = $sermon;
        file_put_contents(
            $this->trackingFilePath,
            json_encode($this->importedAudio)
        );
    }

    /**
     * Gets series ID from Title. If the series doesn't exist it is created
     * @param $seriesTitle
     * @return int
     * @throws \Exception
     * @throws \Throwable
     */
    private function getSeriesId($seriesTitle) : int
    {
        $catGroup = Craft::$app->getCategories()->getGroupByHandle('messageSeries');

        if ($catGroup === null) {
            throw new \Exception('reasons');
        }

        $categoryModel = Category::find()->title($seriesTitle)
            ->group($catGroup)
            ->one();

        if (! $categoryModel) {
            $categoryModel = new Category();
            $categoryModel->groupId = $catGroup->id;
            $categoryModel->fieldLayoutId = $catGroup->fieldLayoutId;
            $categoryModel->title = $seriesTitle;
            $categoryModel->slug = (new Slugify())->slugify($seriesTitle);
            $categoryModel->enabled = true;
            $categoryModel->setScenario(Element::SCENARIO_LIVE);
            Craft::$app->getElements()->saveElement($categoryModel);
        }

        return (int) $categoryModel->id;
    }
}

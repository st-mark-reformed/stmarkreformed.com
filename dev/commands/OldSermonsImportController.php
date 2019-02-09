<?php

namespace dev\commands;

use Craft;
use yii\helpers\Console;
use craft\elements\Entry;
use yii\console\Controller;
use craft\elements\Category;
use dev\services\SermonImporterService;

class OldSermonsImportController extends Controller
{
    public $firstPageOnly = 'false';

    private $batchSize = 100;

    public function options($actionID) : array
    {
        $options = parent::options($actionID);
        $options[] = 'firstPageOnly';
        return $options;
    }

    public function actionRemoveAllSermons()
    {
        $entries = Entry::find();
        $entries->section = 'messages';
        $entries->limit(100);

        $counter = 0;

        foreach ($entries->all() as $entry) {
            $counter++;

            $this->stdout(
                "Deleting entry {$entry->title}..." . PHP_EOL,
                Console::FG_YELLOW
            );

            if ($audioAsset = $entry->audio->one()) {
                Craft::$app->getElements()->deleteElement($audioAsset);
            }

            Craft::$app->getElements()->deleteElement($entry);

            $this->stdout(
                "Deleted entry {$entry->title}..." . PHP_EOL,
                Console::FG_GREEN
            );
        }

        if ($counter) {
            $this->stdout(
                "Finished deleting {$counter} entries. Run command again to continue" . PHP_EOL,
                Console::FG_GREEN
            );

            return;
        }

        $categories = Category::find();
        $categories->group = 'messageSeries';

        $counter = 0;

        foreach ($categories->all() as $category) {
            $counter++;

            $this->stdout(
                "Deleting category {$category->title}..." . PHP_EOL,
                Console::FG_YELLOW
            );

            Craft::$app->getElements()->deleteElement($category);

            $this->stdout(
                "Deleted category {$category->title}..." . PHP_EOL,
                Console::FG_GREEN
            );
        }

        if ($counter) {
            $this->stdout(
                "Finished deleting {$counter} categories. Run command again to continue" . PHP_EOL,
                Console::FG_GREEN
            );

            return;
        }

        $this->stdout(
            'Nothing to do!' . PHP_EOL,
            Console::FG_GREEN
        );
    }

    /**
     * Imports sermons from the old feed
     * @throws \Throwable
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionRunImport()
    {
        Craft::setAlias('@webroot', \dirname(__DIR__, 2) . '/public');

        $firstPageOnly = $this->firstPageOnly === 'true';

        $sermonImporterService = new SermonImporterService();

        $batchDirPath = \dirname(__DIR__) . '/sermonbatches';

        $batchDir = new \DirectoryIterator($batchDirPath);

        $hasBatchFiles = false;
        $batchFiles = [];

        foreach ($batchDir as $fileInfo) {
            if ($fileInfo->getExtension() !== 'json') {
                continue;
            }

            $hasBatchFiles = true;

            $batchFiles[] = "{$batchDirPath}/{$fileInfo->getFilename()}";
        }

        asort($batchFiles);

        if ($hasBatchFiles) {
            $this->stdout(
                'Processing sermon import batches...' . PHP_EOL,
                Console::FG_YELLOW
            );

            $batchFiles = array_reverse($batchFiles);

            $counter = 0;

            foreach ($batchFiles as $batchFile) {
                if ($counter >= $this->batchSize) {
                    break;
                }

                $counter++;

                $this->stdout(
                    "Processing {$batchFile}..." . PHP_EOL,
                    Console::FG_YELLOW
                );

                $sermonImporterService->importSermonFromJsonFile($batchFile);

                $this->stdout(
                    "Finished processing {$batchFile}" . PHP_EOL,
                    Console::FG_GREEN
                );
            }

            $total = 0;

            foreach ($batchDir as $fileInfo) {
                if ($fileInfo->getExtension() !== 'json') {
                    continue;
                }

                $total++;
            }

            if ($total === 0) {
                $this->stdout(
                    'Batch importing is finished' . PHP_EOL,
                    Console::FG_GREEN
                );
                return;
            }

            $this->stdout(
                "Finished processing {$counter} items. " .
                "There are {$total} items left to process. " .
                'Run the command again to continue processing.' . PHP_EOL,
                Console::FG_GREEN
            );
            return;
        }

        $this->stdout(
            'Scraping DOM for sermons. This may take a bit...' . PHP_EOL,
            Console::FG_YELLOW
        );

        $sermonImporterService->scrapeDomForSermons(
            'http://stmarkreformed.com/sermons/',
            'http://stmarkreformed.com/sermons/',
            $batchDirPath,
            $firstPageOnly
        );

        $this->stdout(
            'DOM scrape finished. Run command again to process.' . PHP_EOL,
            Console::FG_GREEN
        );
    }
}

<?php

namespace src\commands;

use Craft;
use yii\helpers\Console;
use craft\elements\Entry;
use yii\console\Controller;
use src\services\NewsImporterService;

class OldNewsImportController extends Controller
{
    public function actionRemoveAllNews()
    {
        $entries = Entry::find();
        $entries->section = ['news', 'pastorsPage'];
        $entries->limit(100);

        $counter = 0;

        foreach ($entries->all() as $entry) {
            $counter++;

            $this->stdout(
                "Deleting entry {$entry->title}..." . PHP_EOL,
                Console::FG_YELLOW
            );

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

        $this->stdout(
            'Nothing to do!' . PHP_EOL,
            Console::FG_GREEN
        );
    }

    /**
     * @throws \Throwable
     */
    public function actionImportNews()
    {
        $this->runImport('news');
    }

    /**
     * @throws \Throwable
     */
    public function actionImportPastorsPage()
    {
        $this->runImport('pastorsPage');
    }

    /**
     * @throws \Throwable
     */
    private function runImport(string $section)
    {
        if ($section !== 'news' && $section !== 'pastorsPage') {
            throw new \Exception('Unrecognized section');
        }

        Craft::setAlias('@webroot', \dirname(__DIR__, 2) . '/public');

        $importerService = new NewsImporterService();

        $batchDirPath =  $section === 'news' ?
            '/newsbatches' :
            '/pastorspagebatches';
        $batchDirPath = \dirname(__DIR__) . $batchDirPath;

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

        if ($hasBatchFiles) {
            $this->stdout(
                'Processing news import batches...' . PHP_EOL,
                Console::FG_YELLOW
            );

            $batchFiles = array_reverse($batchFiles);

            $counter = 0;

            foreach ($batchFiles as $batchFile) {
                if ($counter >= 100) {
                    break;
                }

                $counter++;

                $this->stdout(
                    "Processing {$batchFile}..." . PHP_EOL,
                    Console::FG_YELLOW
                );

                $importerService->importFromJsonFile($batchFile, $section);

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
            'Scraping DOM for news. This may take a bit...' . PHP_EOL,
            Console::FG_YELLOW
        );

        $url = $section === 'news' ?
            'http://stmarkreformed.com/category/news/' :
            'http://stmarkreformed.com/category/pastors-page/';

        $totalPages = $section === 'news' ?
            3 :
            25;

        $importerService->scrapeDomForNews(
            $url,
            $url,
            $batchDirPath,
            1,
            $totalPages
        );

        $this->stdout(
            'DOM scrape finished. Run command again to process.' . PHP_EOL,
            Console::FG_GREEN
        );
    }
}

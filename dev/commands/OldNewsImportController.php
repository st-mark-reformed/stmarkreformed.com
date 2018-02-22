<?php

namespace dev\commands;

use Craft;
use dev\services\NewsImporterService;
use yii\helpers\Console;
use yii\console\Controller;

/**
 * Class OldNewsImportController
 */
class OldNewsImportController extends Controller
{
    /**
     * Imports news from the old site
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionRunImport()
    {
        Craft::setAlias('@webroot', \dirname(__DIR__, 2) . '/public');

        $importerService = new NewsImporterService();

        $batchDirPath = \dirname(__DIR__) . '/newsbatches';

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
                if ($counter >= 20) {
                    break;
                }

                $counter++;

                $this->stdout(
                    "Processing {$batchFile}..." . PHP_EOL,
                    Console::FG_YELLOW
                );

                $importerService->importFromJsonFile($batchFile, 'pastorsPage');

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

        // $importerService->scrapeDomForNews(
        //     'http://stmarkreformed.com/category/news/',
        //     'http://stmarkreformed.com/category/news/',
        //     $batchDirPath,
        //     1,
        //     3
        // );

        $importerService->scrapeDomForNews(
            'http://stmarkreformed.com/category/pastors-page/',
            'http://stmarkreformed.com/category/pastors-page/',
            $batchDirPath,
            1,
            25
        );

        $this->stdout(
            'DOM scrape finished. Run command again to process.' . PHP_EOL,
            Console::FG_GREEN
        );
    }
}

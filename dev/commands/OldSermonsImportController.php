<?php

namespace dev\commands;

use Craft;
use yii\helpers\Console;
use yii\console\Controller;
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

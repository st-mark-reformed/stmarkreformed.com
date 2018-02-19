<?php

namespace dev\commands;

use dev\services\SermonImporterService;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * OldSermonsImport command
 */
class OldSermonsImportController extends Controller
{
    public $firstPageOnly = 'true';

    /**
     * @inheritdoc
     */
    public function options($actionID) : array
    {
        $options = parent::options($actionID);
        $options[] = 'firstPageOnly';
        return $options;
    }

    /**
     * Imports sermons from the old feed
     * @throws \Exception
     */
    public function actionRunImport()
    {
        $firstPageOnly = $this->firstPageOnly === 'true';

        $batchDirPath = \dirname(__DIR__) . '/sermonbatches';

        $batchDir = new \DirectoryIterator($batchDirPath);

        $hasBatchFiles = false;

        foreach ($batchDir as $fileInfo) {
            if ($fileInfo->getExtension() !== 'json') {
                continue;
            }

            $hasBatchFiles = true;

            break;
        }

        if ($hasBatchFiles) {
            // TODO: Process batch files
            var_dump('TODO: Process batch files');
            die;
        }

        $this->stdout(
            'Scraping DOM for sermons. This may take a bit...' . PHP_EOL,
            Console::FG_YELLOW
        );

        (new SermonImporterService())->scrapeDomForSermons(
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

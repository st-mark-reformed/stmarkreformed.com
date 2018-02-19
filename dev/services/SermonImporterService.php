<?php

namespace dev\services;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class SermonImporterService
 */
class SermonImporterService
{
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
}

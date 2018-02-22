<?php

namespace dev\services;

use Craft;
use craft\elements\Entry;
use Cocur\Slugify\Slugify;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Class NewsImporterService
 */
class NewsImporterService
{
    /**
     * Scrapes URL for news on stmarkreformed.com
     * @param string $url
     * @param string $baseUrl
     * @param string $batchDirPath
     * @param int $pageNum
     * @param int $pages
     * @throws \Exception
     */
    public function scrapeDomForNews(
        string $url,
        string $baseUrl,
        string $batchDirPath,
        int $pageNum = 1,
        int $pages = 1
    ) {
        $guzzleClient = new GuzzleClient();

        $page = $guzzleClient->request('GET', $url);

        $domDoc = new \DOMDocument();

        @$domDoc->loadHTML($page->getBody()->getContents());

        foreach ($domDoc->getElementsByTagName('article') as $article) {
            /** @var \DOMElement $article */

            $items = [];

            foreach ($article->childNodes as $element) {
                /** @var \DOMElement $element */
                if (\get_class($element) !== 'DOMElement') {
                    continue;
                }

                switch ($element->tagName) {
                    case 'h1':
                        $items = ['body' => ''];
                        $items['title'] = $element->textContent;
                        break;
                    default:
                        $class = $element->getAttribute('class');
                        if ($class === 'byline') {
                            $parts = explode('|', $element->textContent);
                            $items['author'] = trim($parts[0]);
                            $parts = explode(' Posted ', $parts[1]);
                            $items['date'] = $parts[1];
                            break;
                        } elseif ($class === 'postMeta') {
                            file_put_contents(
                                $batchDirPath . '/' . microtime(true) . '.json',
                                json_encode($items)
                            );
                            break;
                        }

                        $items['body'] .= $element->ownerDocument->saveHTML($element);
                }
            }
        }

        if ($pageNum >= $pages) {
            return;
        }

        $pageNum++;

        $this->scrapeDomForNews(
            "{$baseUrl}page/{$pageNum}/",
            $baseUrl,
            $batchDirPath,
            $pageNum,
            $pages
        );
    }

    /**
     * Imports a new item from json file path
     * @param string $filePath
     * @param string $sectionHandle
     * @throws \Exception
     * @throws \Throwable
     */
    public function importFromJsonFile(
        string $filePath,
        string $sectionHandle = 'news'
    ) {
        $json = json_decode(file_get_contents($filePath));

        // $body = new \DOMElement('div', $json->body);
        $body = new \DOMDocument();

        $body->loadHTML("<div class=\"importWrapper\">{$json->body}</div>");

        $images = [];

        foreach ($body->getElementsByTagName('img') as $img) {
            /** @var \DOMElement $img */
            $images[] = $img->getAttribute('src');
        }

        $guzzleClient = new GuzzleClient();

        $webroot = Craft::getAlias('@webroot');

        if ($images) {
            foreach ($images as $url) {
                $parsed = parse_url($url);
                $path = ltrim($parsed['path'], '/');
                $fullPath = "{$webroot}/{$path}";
                $dirName = \dirname($fullPath);
                exec("mkdir -p {$dirName}");
                try {
                    $guzzleClient->get($url, [
                        'save_to' => $fullPath,
                    ]);
                } catch (\Exception $e) {
                    try {
                        $guzzleClient->get("http://stmarkreformed.com/{$url}", [
                            'save_to' => $fullPath,
                        ]);
                    } catch (\Exception $e) {
                        //
                    }
                }
            }
        }

        $section = Craft::$app->getSections()->getSectionByHandle($sectionHandle);

        if ($section === null) {
            throw new \Exception('reasons');
        }

        $date = new \DateTime();
        $date->setTimestamp(strtotime("{$json->date} 11:00am"));

        $entry = new Entry();
        $entry->sectionId = $section->id;
        $entry->typeId = $section->getEntryTypes()[0]->id;
        $entry->slug = (new Slugify())->slugify($json->title);
        $entry->postDate = $date;
        $entry->enabled = true;
        $entry->enabledForSite = true;
        $entry->title = $json->title;

        $bodyHtml = '';

        foreach ($body->getElementsByTagName('div') as $div) {
            /** @var \DOMElement $div */
            if ($div->getAttribute('class') !== 'importWrapper') {
                continue;
            }

            foreach ($div->childNodes as $childNode) {
                /** @var \DOMElement $childNode */
                $bodyHtml .= $childNode->ownerDocument->saveHTML($childNode);
            }
        }

        $entry->setFieldValue('entryBuilder', [
            'new1' => [
                'type' => 'basicEntryBlock',
                'enabled' => '1',
                'fields' => [
                    'heading' => '',
                    'subheading' => '',
                    'body' => $bodyHtml,
                ],
            ],
        ]);

        Craft::$app->getElements()->saveElement($entry);

        unlink($filePath);
    }
}

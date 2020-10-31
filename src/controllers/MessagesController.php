<?php

namespace src\controllers;

use Craft;
use yii\web\Response;
use craft\elements\User;
use craft\elements\Entry;
use yii\web\HttpException;
use craft\elements\Category;
use craft\elements\db\EntryQuery;
use craft\elements\db\AssetQuery;
use src\services\PaginationService;

/**
 * Class MessagesController
 */
class MessagesController extends BaseController
{
    /**
     * Messages index
     * @param int $pageNum
     * @param string $speaker
     * @param string $series
     * @param string $filter
     * @return Response
     * @throws \Exception
     */
    public function actionIndex(
        int $pageNum = null,
        string $speaker = null,
        string $series = null,
        string $filter = null
    ) : Response {
        if ($pageNum === 1) {
            throw new HttpException(404);
        }

        $breadCrumbs = [];

        $pageNum = $pageNum ?: 1;
        $metaTitle = 'Messages';
        $heroHeading = 'Messages from St. Mark';
        $activeSpeaker = null;
        $activeSeries = null;
        $limit = 10;

        $entriesQuery = Entry::find()->section('messages');

        if ($filter === 'filter') {
            $this->filterEntryQuery($entriesQuery);

            $breadCrumbs = [
                [
                    'href' => '/',
                    'content' => 'Home',
                ],
                [
                    'href' => '/media/messages',
                    'content' => 'Messages',
                ],
                [
                    'href' => '/media/messages/filter?' . Craft::$app->getRequest()->getQueryString(),
                    'content' => 'Filtered',
                ],
            ];
        }

        if ($speaker) {
            $speakerQuery = User::find()->slugField($speaker)->one();

            if (! $speakerQuery) {
                throw new HttpException(404);
            }

            $entriesQuery->relatedTo([
                'targetElement' => $speakerQuery,
                'field' => 'speaker',
            ]);

            $speakerName = "{$speakerQuery->titleOrHonorific} ";
            $speakerName .= $speakerQuery->getFullName();
            $speakerName = trim($speakerName);

            $heroHeading = $metaTitle = "Messages by {$speakerName}";

            $activeSpeaker = $speakerQuery->slugField;

            $breadCrumbs = [
                [
                    'href' => '/',
                    'content' => 'Home',
                ],
                [
                    'href' => '/media/messages',
                    'content' => 'Messages',
                ],
                [
                    'href' => '/media/messages/by/' . $activeSpeaker,
                    'content' => 'By ' . $speakerName,
                ],
            ];
        }

        if ($series) {
            $seriesQuery = Category::find()->group('messageSeries')
                ->slug($series)
                ->one();

            if (! $seriesQuery) {
                throw new HttpException(404);
            }

            $entriesQuery->relatedTo([
                'targetElement' => $seriesQuery,
                'field' => 'messageSeries',
            ]);

            $heroHeading = $metaTitle = "Messages series: {$seriesQuery->title}";

            $activeSeries = $seriesQuery->slug;

            $breadCrumbs = [
                [
                    'href' => '/',
                    'content' => 'Home',
                ],
                [
                    'href' => '/media/messages',
                    'content' => 'Messages',
                ],
                [
                    'href' => '/media/messages/series/' . $activeSeries,
                    'content' => 'Series: ' . $seriesQuery->title,
                ],
            ];
        }

        $entriesTotal = (int) $entriesQuery->count();
        $maxPages = (int) ceil($entriesTotal / $limit);

        if ($pageNum > 1 && $pageNum > $maxPages) {
            throw new HttpException(404);
        }

        $offset = ($limit * $pageNum) - $limit;

        $entries = $entriesQuery->limit($limit)->offset($offset)->all();

        $pagination = PaginationService::getPagination([
            'currentPage' => $pageNum,
            'perPage' => $limit,
            'totalResults' => $entriesTotal,
            'base' => PaginationService::getUriPathSansPagination()
        ]);

        if ($pageNum > 1) {
            $metaTitle .= " | Page {$pageNum}";

            if (! $breadCrumbs) {
                $breadCrumbs = [
                    [
                        'href' => '/',
                        'content' => 'Home',
                    ],
                    [
                        'href' => '/media/messages',
                        'content' => 'Messages',
                    ],
                ];
            }

            $breadCrumbs[] = [
                'content' => 'Page ' . $pageNum,
            ];
        }

        $request = Craft::$app->getRequest();

        $filterValues = [
            'messages_by' => $request->get('messages_by'),
            'messages_in_series' => $request->get('messages_in_series'),
            'messages_scripture_reference' => $request->get('messages_scripture_reference'),
            'messages_title' => $request->get('messages_title'),
            'messages_date_range_start' => $request->get('messages_date_range_start'),
            'messages_date_range_end' => $request->get('messages_date_range_end'),
        ];

        $response = $this->renderTemplate(
            '_core/Messages.twig',
            compact(
                'breadCrumbs',
                'metaTitle',
                'heroHeading',
                'activeSpeaker',
                'activeSeries',
                'entries',
                'pagination',
                'filterValues'
            ),
            $filter !== 'filter'
        );

        return $response;
    }

    /**
     * Renders the message single entry page
     * @param Entry $entry
     * @return Response
     * @throws \Exception
     */
    public function actionEntry(Entry $entry) : Response
    {
        /** @var AssetQuery $shareImageQuery */
        $shareImageQuery = $entry->customShareImage;

        $shareImage = null;

        if ($shareImageAsset = $shareImageQuery->one()) {
            $shareImage = $shareImageAsset->getUrl([
                'width' => min(1000, $shareImageAsset->width),
            ]);
        }

        return $this->renderTemplate('_core/Messages.twig', [
            'noIndex' => ! $entry->searchEngineIndexing,
            'metaTitle' => ($entry->seoTitle ?: $entry->title) . ' | Messages',
            'metaDescription' => $entry->seoDescription,
            'shareImage' => $shareImage,
            'heroHeading' => $entry->heroHeading ?: $entry->title,
            'entries' => [$entry],
            'showFilters' => false,
            'breadCrumbs' => [
                [
                    'href' => '/',
                    'content' => 'Home',
                ],
                [
                    'href' => '/media/messages',
                    'content' => 'Messages',
                ],
                [
                    'content' => 'Viewing Message',
                ],
            ],
        ]);
    }

    private function filterEntryQuery(EntryQuery $query)
    {
        $request = Craft::$app->getRequest();

        $by = $request->get('messages_by');
        $series = $request->get('messages_in_series');
        $ref = $request->get('messages_scripture_reference');
        $title = $request->get('messages_title');
        $start = $request->get('messages_date_range_start');
        $end = $request->get('messages_date_range_end');

        if (! $by && ! $series && ! $ref && ! $title && ! $start && ! $end) {
            throw new HttpException(404);
        }

        $relatedTo = [];

        if ($by) {
            $by = is_array($by) ? $by : [$by];
            $relatedTo = array_merge(
                $relatedTo,
                User::find()->slugField($by)->ids()
            );
        }

        if ($series) {
            $series = is_array($series) ? $series : [$series];
            $relatedTo = array_merge(
                $relatedTo,
                Category::find()->slug($series)->ids()
            );
        }

        if ($relatedTo) {
            $query->relatedTo($relatedTo);
        }

        $searchStr = '';

        if ($ref) {
            $searchStr .= ' messageText:"*' . $ref . '*"';
        }

        if ($title) {
            $searchStr .= ' title:"*' . $title . '*"';
        }

        if ($searchStr) {
            $query->search(trim($searchStr));
        }

        // Hack because apparently Craft end date with <= doesn't effing work
        if ($end) {
            $end = date_create($end);
            $end->setTimestamp($end->getTimestamp() + 86400);
            $end = $end->format('Y-m-d');
        }

        if ($start && $end) {
            $query->postDate(['and', '>= ' . $start, '< ' . $end]);
            return;
        }

        if ($start) {
            $query->postDate('>= '. $start);
        }

        if ($end) {
            $query->postDate('<= ' . $end);
        }
    }
}

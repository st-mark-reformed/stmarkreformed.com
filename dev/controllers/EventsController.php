<?php

namespace dev\controllers;

use yii\web\Response;
use craft\elements\Entry;
use yii\web\HttpException;
use craft\helpers\Template;
use craft\elements\db\AssetQuery;
use dev\services\PaginationService;

/**
 * Class GalleriesController
 */
class EventsController extends BaseController
{
    /**
     * Renders a listing of events
     * @param int $pageNum
     * @return Response
     * @throws \Exception
     */
    public function actionIndex(int $pageNum = null) : Response
    {
        if ($pageNum === 1) {
            throw new HttpException(404);
        }

        $pageNum = $pageNum ?: 1;
        $metaTitle = 'Events' . ($pageNum > 1 ? " | Page {$pageNum}" : '');
        $heroHeading = 'Events';
        $limit = 12;
        $dateType = 'event';
        $bodyType = 'event';

        $dayAfter = new \DateTime();
        $dayAfter->setTimestamp(strtotime('-1 day'));
        $dayAfter = $dayAfter->format('Y-m-d G:i:s');

        $entriesQuery = Entry::find()->section('events')
            ->endDate("> {$dayAfter}");

        $entriesTotal = (int) $entriesQuery->count();
        $maxPages = (int) ceil($entriesTotal / $limit);

        if ($pageNum > $maxPages) {
            throw new HttpException(404);
        }

        $offset = ($limit * $pageNum) - $limit;

        $entries = $entriesQuery->limit($limit)
            ->offset($offset)
            ->orderBy('startDate asc')
            ->all();

        $pagination = PaginationService::getPagination([
            'currentPage' => $pageNum,
            'perPage' => $limit,
            'totalResults' => $entriesTotal,
            'base' => PaginationService::getUriPathSansPagination()
        ]);

        $response = $this->renderTemplate(
            '_core/StandardListing',
            compact(
                'metaTitle',
                'heroHeading',
                'entries',
                'pagination',
                'dateType',
                'bodyType'
            ),
            false
        );

        return $response;
    }

    /**
     * Renders the gallery single entry page
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

        $dateString = '';

        if ($entry->allDayEvent) {
            $startDateString = $entry->startDate->format('F j, Y');
            $endDateString = $entry->endDate->format('F j, Y');
            $dateString = $startDateString;

            if ($startDateString !== $endDateString) {
                $dateString .= " &mdash; {$endDateString}";
            }
        }

        if (! $dateString) {
            $startDateString = $entry->startDate->format('F j, Y');
            $startDateHM = $entry->startDate->format('g:i a');
            $endDateString = $entry->endDate->format('F j, Y');
            $endDateHM = $entry->endDate->format('g:i a');

            $dateString = "{$startDateString}, {$startDateHM}";

            if ($startDateString !== $endDateString ||
                $startDateHM !== $endDateHM
            ) {
                $dateString .= ' &mdash; ';

                if ($startDateString !== $endDateString) {
                    $dateString .= "{$endDateString}, ";
                }

                $dateString .= $endDateHM;
            }
        }

        $dateStringReplace = str_replace('&mdash;', '-', $dateString);

        return $this->renderTemplate('_events/entry', [
            'noIndex' => ! $entry->searchEngineIndexing,
            'metaTitle' => ($entry->seoTitle ?: $entry->title) .
                " | {$dateStringReplace} | Events",
            'metaDescription' => $entry->seoDescription,
            'shareImage' => $shareImage,
            'heroHeading' => $entry->heroHeading ?: $entry->title,
            'heroImageAsset' => $entry->heroImage->one(),
            'entry' => $entry,
            'dateString' => Template::raw($dateString),
        ]);
    }
}

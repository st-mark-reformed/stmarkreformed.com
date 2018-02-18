<?php

namespace dev\controllers;

use yii\web\Response;
use craft\elements\Entry;
use yii\web\HttpException;
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
        $heroHeading = 'Events';
        $limit = 12;

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

        $response = $this->renderTemplate('_events/index', compact(
            'heroHeading',
            'entries',
            'pagination'
        ));

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

        return $this->renderTemplate('_events/entry', [
            'noIndex' => ! $entry->searchEngineIndexing,
            'metaTitle' => $entry->seoTitle ?? $entry->title,
            'metaDescription' => $entry->seoDescription,
            'shareImage' => $shareImage,
            'heroHeading' => $entry->title,
            'heroImageAsset' => $entry->heroImage->one(),
            'entry' => $entry,
        ]);
    }
}

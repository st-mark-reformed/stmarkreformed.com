<?php

namespace dev\controllers;

use yii\web\Response;
use craft\elements\Entry;
use yii\web\HttpException;
use dev\services\PaginationService;

/**
 * Class NewsController
 */
class NewsController extends BaseController
{
    /**
     * Renders a listing of news items
     * @param int|null $pageNum
     * @return Response
     * @throws \Exception
     */
    public function actionIndex(int $pageNum = null) : Response
    {
        if ($pageNum === 1) {
            throw new HttpException(404);
        }

        $pageNum = $pageNum ?: 1;
        $heroHeading = 'News';
        $limit = 12;
        $dateType = 'entry';
        $bodyType = 'entry';

        $entriesQuery = Entry::find()->section('news');

        $entriesTotal = (int) $entriesQuery->count();
        $maxPages = (int) ceil($entriesTotal / $limit);

        if ($pageNum > $maxPages) {
            throw new HttpException(404);
        }

        $offset = ($limit * $pageNum) - $limit;

        $entries = $entriesQuery->limit($limit)
            ->offset($offset)
            ->all();

        $pagination = PaginationService::getPagination([
            'currentPage' => $pageNum,
            'perPage' => $limit,
            'totalResults' => $entriesTotal,
            'base' => PaginationService::getUriPathSansPagination()
        ]);

        return $this->renderTemplate('_core/StandardListing', compact(
            'heroHeading',
            'entries',
            'pagination',
            'dateType',
            'bodyType'
        ));
    }
}

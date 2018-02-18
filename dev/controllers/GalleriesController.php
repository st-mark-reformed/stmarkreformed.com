<?php

namespace dev\controllers;

use yii\web\Response;
use craft\elements\Entry;
use yii\web\HttpException;
use dev\services\PaginationService;

/**
 * Class GalleriesController
 */
class GalleriesController extends BaseController
{
    /**
     * Renders a listing of the galleries
     * @param int $pageNum
     * @return Response
     * @throws \Exception
     */
    public function actionIndex(int $pageNum = null) : Response
    {
        if ($pageNum === 1) {
            throw new HttpException(404);
        }

        $shouldCache = true;
        $pageNum = $pageNum ?: 1;
        $heroHeading = 'Galleries';
        $limit = 10;

        $entriesQuery = Entry::find()->section('galleries');

        $entriesTotal = (int) $entriesQuery->count();
        $maxPages = (int) ceil($entriesTotal / $limit);

        if ($pageNum > $maxPages) {
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

        // TODO: move caching here
        $response = $this->renderTemplate('_galleries/index', compact(
            'shouldCache',
            'heroHeading',
            'entries',
            'pagination'
        ));

        return $response;
    }
}

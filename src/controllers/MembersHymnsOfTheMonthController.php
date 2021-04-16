<?php

namespace src\controllers;

use craft\elements\Entry;
use src\services\PaginationService;
use yii\web\HttpException;
use yii\web\Response;

class MembersHymnsOfTheMonthController extends MembersBaseController
{
    public function actionIndex(int $pageNum = null) : Response
    {
        if ($pageNum === 1) {
            throw new HttpException(404);
        }

        $pageNum = $pageNum ?: 1;
        $metaTitle = 'Hymns of the Month' . ($pageNum > 1 ? " | Page {$pageNum}" : '');
        $heroHeading = 'Hymns of the Month';
        $limit = 12;
        $dateType = 'hymnOfTheMonth';
        $bodyType = 'hymnOfTheMonth';

        $section = 'hymnsOfTheMonth';

        $entriesQuery = Entry::find()->section($section);

        $entriesTotal = (int) $entriesQuery->count();
        $maxPages = (int) ceil($entriesTotal / $limit);

        if (! $entriesTotal) {
            return $this->renderTemplate(
                '_core/NoEntries.twig',
                [
                    'metaTitle' => $metaTitle,
                    'heroHeading' => 'No Entries Found',
                ]
            );
        }

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

        return $this->renderTemplate(
            '_core/ListingStandard.twig',
                compact(
                'metaTitle',
                'heroHeading',
                'entries',
                'pagination',
                'dateType',
                'bodyType'
            )
        );
    }

    public function actionEntry(Entry $entry) : Response
    {
        dd($entry);
    }
}

<?php

namespace src\controllers;

use craft\elements\Asset;
use craft\elements\Entry;
use src\services\PaginationService;
use yii\web\HttpException;
use yii\web\Response;

class MembersHymnsOfTheMonthController extends MembersBaseController
{
    /**
     * @param int|null $pageNum
     * @return Response
     * @throws HttpException
     */
    public function actionIndex(int $pageNum = null) : Response
    {
        if (! $this->isLoggedIn) {
            return $this->response;
        }

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

        $breadCrumbs = [
            [
                'href' => '/',
                'content' => 'Home',
            ],
            [
                'href' => '/members',
                'content' => 'Members',
            ],
            [
                'content' => 'Hymns of the Month',
            ],
        ];

        return $this->renderTemplate(
            '_core/ListingStandard.twig',
                compact(
                'metaTitle',
                'heroHeading',
                'entries',
                'pagination',
                'dateType',
                'bodyType',
                'breadCrumbs'
            )
        );
    }

    public function actionEntry(Entry $entry) : Response
    {
        if (! $this->isLoggedIn) {
            return $this->response;
        }

        $musicDownload = '';

        $musicAsset = $entry->hymnOfTheMonthMusic->one();

        if ($musicAsset !== null) {
            $musicDownload = '/members/hymns-of-the-month/' . $musicAsset->getPath();
        }

        $practiceTracks = array_map(
            function (Asset  $asset) {
                return [
                    'href' => '/members/hymns-of-the-month/' . $asset->getPath(),
                    'content' => $asset->title,
                ];
            },
            $entry->hymnOfTheMonthPracticeTracks->all(),
        );

        return $this->renderTemplate(
            '_core/MembersHymnOfTheMonth.twig',
            [
                'heroHeading' => 'Hymn of the Month for ' .
                    $entry->date->format('F, Y'),
                'heroSubheading' => $entry->hymnPsalmName,
                'musicDownload' => $musicDownload,
                'practiceTracks' => $practiceTracks,
                'breadCrumbs' => [
                    [
                        'href' => '/',
                        'content' => 'Home',
                    ],
                    [
                        'href' => '/members',
                        'content' => 'Members',
                    ],
                    [
                        'href' => '/members/hymns-of-the-month',
                        'content' => 'Hymns of the Month',
                    ],
                    [
                        'content' => 'Viewing Entry',
                    ],
                ],
            ]
        );
    }
}

<?php

namespace dev\controllers;

use Exception;
use yii\web\Response;
use craft\elements\Entry;
use yii\web\HttpException;
use craft\elements\db\AssetQuery;
use dev\services\PaginationService;

class ResourcesController extends BaseController
{
    /**
     * Renders a listing of news items
     * @param string $section
     * @param int|null $pageNum
     * @return Response
     * @throws Exception
     */
    public function actionIndex(
        int $pageNum = null
    ) : Response {
        if ($pageNum === 1) {
            throw new HttpException(404);
        }

        $pageNum = $pageNum ?: 1;
        $metaTitle = 'Resources' . ($pageNum > 1 ? " | Page {$pageNum}" : '');
        $heroHeading = 'Resources';
        $limit = 12;

        $entriesQuery = Entry::find()->section('resources');

        $entriesTotal = (int) $entriesQuery->count();
        $maxPages = (int) ceil($entriesTotal / $limit);

        if (! $entriesTotal) {
            return $this->renderTemplate('_core/NoEntries.twig', [
                'metaTitle' => $metaTitle,
                'heroHeading' => 'No Entries Found',
            ]);
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

        return $this->renderTemplate('_core/ResourcesListingStandard.twig', compact(
            'metaTitle',
            'heroHeading',
            'entries',
            'pagination'
        ));
    }

    /**
     * Renders a news item single entry page
     * @param Entry $entry
     * @return Response
     * @throws Exception
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

        return $this->renderTemplate(
            '_core/ResourceEntryStandard.twig',
            [
                'noIndex' => ! $entry->searchEngineIndexing,
                'metaTitle' => ($entry->seoTitle ?: $entry->title) . ' | Resources',
                'metaDescription' => $entry->seoDescription,
                'shareImage' => $shareImage,
                'heroHeading' => $entry->heroHeading ?: $entry->title,
                'entry' => $entry,
                'breadCrumbs' => [
                    [
                        'href' => '/',
                        'content' => 'Home',
                    ],
                    [
                        'href' => '/resources',
                        'content' => 'Resources',
                    ],
                    [
                        'content' => 'Viewing Resource',
                    ],
                ],
            ]
        );
    }
}

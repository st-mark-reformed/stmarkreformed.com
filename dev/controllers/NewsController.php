<?php

namespace dev\controllers;

use yii\web\Response;
use craft\elements\Entry;
use yii\web\HttpException;
use craft\elements\db\AssetQuery;
use dev\services\PaginationService;

/**
 * Class NewsController
 */
class NewsController extends BaseController
{
    /**
     * Renders a listing of news items
     * @param string $section
     * @param int|null $pageNum
     * @return Response
     * @throws \Exception
     */
    public function actionIndex(
        string $section = null,
        int $pageNum = null
    ) : Response {
        if ($pageNum === 1) {
            throw new HttpException(404);
        }

        $pageNum = $pageNum ?: 1;
        $metaTitle = 'News' . ($pageNum > 1 ? " | Page {$pageNum}" : '');
        $heroHeading = 'News';
        $limit = 12;
        $dateType = 'entry';
        $bodyType = 'entry';

        $section = $section ?? 'news';

        $entriesQuery = Entry::find()->section($section);

        $entriesTotal = (int) $entriesQuery->count();
        $maxPages = (int) ceil($entriesTotal / $limit);

        if (! $entriesTotal) {
            return $this->renderTemplate('_core/NoEntries', [
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

        return $this->renderTemplate('_core/StandardListing', compact(
            'metaTitle',
            'heroHeading',
            'entries',
            'pagination',
            'dateType',
            'bodyType'
        ));
    }

    /**
     * Renders a news item single entry page
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

        return $this->renderTemplate('_core/StandardEntry', [
            'noIndex' => ! $entry->searchEngineIndexing,
            'metaTitle' => ($entry->seoTitle ?: $entry->title) . ' | News',
            'metaDescription' => $entry->seoDescription,
            'shareImage' => $shareImage,
            'heroHeading' => $entry->title,
            'heroImageAsset' => $entry->heroImage->one(),
            'entry' => $entry,
            'backLink' => '/news',
            'backLinkText' => 'back to all news'
        ]);
    }
}

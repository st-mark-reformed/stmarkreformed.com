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
        string $section = null,
        int $pageNum = null
    ) : Response {
        dd('TODO: Index');
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

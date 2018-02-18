<?php

namespace dev\controllers;

use Craft;
use yii\web\Response;
use craft\elements\Entry;
use craft\elements\db\AssetQuery;

/**
 * Class PagesController
 */
class PagesController extends BaseController
{
    /**
     * Renders a page
     * @param Entry $entry
     * @return Response
     * @throws \Exception
     */
    public function actionPage(Entry $entry) : Response
    {
        /** @var AssetQuery $shareImageQuery */
        $shareImageQuery = $entry->customShareImage;

        $shareImage = null;

        if ($shareImageAsset = $shareImageQuery->one()) {
            $shareImage = $shareImageAsset->getUrl([
                'width' => min(1000, $shareImageAsset->width),
            ]);
        }

        $metaTitle = $entry->seoTitle;

        if (! $metaTitle && Craft::$app->getRequest()->getSegment(1)) {
            $metaTitle = $entry->title;
        }

        // TODO: move caching here
        return $this->renderTemplate('_core/StandardPage', [
            'shouldCache' => true,
            'noIndex' => ! $entry->searchEngineIndexing,
            'metaTitle' => $metaTitle,
            'metaDescription' => $entry->seoDescription,
            'shareImage' => $shareImage,
            'heroImageAsset' => $entry->heroImage->one(),
            'heroHeading' => $entry->title,
            'heroSubheading' => $entry->heroSubheading,
            'entry' => $entry,
        ]);
    }
}

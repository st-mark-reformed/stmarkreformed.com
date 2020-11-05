<?php

namespace src\controllers;

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

        // Hack for now to not cache the contact page
        $cache = Craft::$app->getRequest()->getSegment(1) !== 'contact';

        // More hacking not to cache any pages with a stripe form
        if ($cache) {
            foreach ($entry->standardPageBuilder->all() as $pageBlock) {
                if ($pageBlock->getType()->handle === 'stripeForm') {
                    $cache = false;

                    break;
                }
            }
        }

        return $this->renderTemplate(
            '_core/PageStandard.twig',
            [
                'noIndex' => ! $entry->searchEngineIndexing,
                'metaTitle' => $metaTitle,
                'metaDescription' => $entry->seoDescription,
                'shareImage' => $shareImage,
                'heroImageAsset' => $entry->heroImage->one(),
                'useShortHeader' => $entry->useShortHeader,
                'heroHeading' => $entry->heroHeading ?: $entry->title,
                'heroSubheading' => $entry->heroSubheading,
                'entry' => $entry,
            ],
            $cache
        );
    }
}

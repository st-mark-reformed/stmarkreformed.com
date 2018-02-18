<?php

namespace dev\controllers;

use yii\web\Response;
use craft\elements\User;
use craft\elements\Entry;
use yii\web\HttpException;
use craft\elements\Category;
use craft\elements\db\AssetQuery;
use dev\services\PaginationService;

/**
 * Class MessagesController
 */
class MessagesController extends BaseController
{
    /**
     * Messages index
     * @param int $pageNum
     * @param string $speaker
     * @param string $series
     * @return Response
     * @throws \Exception
     */
    public function actionIndex(
        int $pageNum = null,
        string $speaker = null,
        string $series = null
    ) : Response {
        if ($pageNum === 1) {
            throw new HttpException(404);
        }

        $shouldCache = true;
        $pageNum = $pageNum ?: 1;
        $backLink = null;
        $backLinkText = 'back to all messages';
        $metaTitle = 'Messages';
        $heroHeading = 'Messages from St. Mark';
        $activeSpeaker = null;
        $activeSeries = null;
        $limit = 10;

        $entriesQuery = Entry::find()->section('messages');

        if ($speaker) {
            $speakerQuery = User::find()->slugField($speaker)->one();

            if (! $speakerQuery) {
                throw new HttpException(404);
            }

            $entriesQuery->relatedTo([
                'targetElement' => $speakerQuery,
                'field' => 'speaker',
            ]);

            $speakerName = "{$speakerQuery->titleOrHonorific} ";
            $speakerName .= $speakerQuery->getFullName();
            $speakerName = trim($speakerName);

            $heroHeading = $metaTitle = "Messages by {$speakerName}";

            $activeSpeaker = $speakerQuery->slugField;

            $backLink = '/media/messages';
        }

        if ($series) {
            $seriesQuery = Category::find()->group('messageSeries')
                ->slug($series)
                ->one();

            if (! $seriesQuery) {
                throw new HttpException(404);
            }

            $entriesQuery->relatedTo([
                'targetElement' => $seriesQuery,
                'field' => 'messageSeries',
            ]);

            $heroHeading = $metaTitle = "Messages series: {$seriesQuery->title}";

            $activeSeries = $seriesQuery->slug;

            $backLink = '/media/messages';
        }

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
        $response = $this->renderTemplate('_audio/index', compact(
            'shouldCache',
            'backLink',
            'backLinkText',
            'metaTitle',
            'heroHeading',
            'activeSpeaker',
            'activeSeries',
            'entries',
            'pagination'
        ));

        return $response;
    }

    /**
     * Renders the message single entry page
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

        // TODO: move caching here
        return $this->renderTemplate('_audio/index', [
            'shouldCache' => true,
            'noIndex' => ! $entry->searchEngineIndexing,
            'metaTitle' => $entry->seoTitle ?? $entry->title,
            'metaDescription' => $entry->seoDescription,
            'shareImage' => $shareImage,
            'heroHeading' => $entry->title,
            'entries' => [$entry],
            'backLink' => '/media/messages',
            'backLinkText' => 'back to all messages',
        ]);
    }
}

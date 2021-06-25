<?php

namespace src\controllers;

use craft\elements\Entry;
use src\httphelpers\ServeFileDownload;
use src\services\PaginationService;
use yii\web\HttpException;
use yii\web\Response;

class InternalMessagesController extends MembersBaseController
{
    public function actionIndex(
        int $pageNum = null
    ) : Response {
        if (! $this->isLoggedIn) {
            return $this->response;
        }

        if ($pageNum === 1) {
            throw new HttpException(404);
        }

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
                'href' => '/members/internal-messages',
                'content' => 'Internal Messages',
            ],
        ];

        $pageNum = $pageNum ?: 1;

        $metaTitle = 'Internal Messages';
        $heroHeading = 'Internal Messages from St. Mark';
        $limit = 10;

        $entriesQuery = Entry::find()->section('internalMessages');

        $entriesTotal = (int) $entriesQuery->count();
        $maxPages = (int) ceil($entriesTotal / $limit);

        if ($pageNum > 1 && $pageNum > $maxPages) {
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

        if ($pageNum > 1) {
            $metaTitle .= " | Page {$pageNum}";

            $breadCrumbs[] = [
                'content' => 'Page ' . $pageNum,
            ];
        }

        return $this->renderTemplate(
            '_core/InternalMessages.twig',
            compact(
                'breadCrumbs',
                'metaTitle',
                'heroHeading',
                'entries',
                'pagination'
            ),
            true
        );
    }

    public function actionEntry(Entry $entry): Response
    {
        if (! $this->isLoggedIn) {
            return $this->response;
        }

        return $this->renderTemplate('_core/InternalMessages.twig', [
            'noIndex' => ! $entry->searchEngineIndexing,
            'metaTitle' => $entry->title . ' | Messages',
            'heroHeading' => $entry->heroHeading ?: $entry->title,
            'entries' => [$entry],
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
                    'href' => '/members/internal-messages',
                    'content' => 'Internal Messages',
                ],
                [
                    'content' => 'Viewing Messages',
                ],
            ],
        ]);
    }

    public function actionDownloadFile(string $slug): Response
    {
        if (! $this->isLoggedIn) {
            return $this->response;
        }

        $section = 'internalMessages';

        $entry = Entry::find()->section($section)
            ->slug($slug)
            ->one();

        if ($entry === null) {
            throw new HttpException(404);
        }

        $asset = $entry->internalAudio->one();

        if ($asset === null) {
            throw new HttpException(404);
        }

        // OH MY LANTA THIS IS DIRTY. I should do something about this.
        $fullPath = CRAFT_BASE_PATH . '/filesAboveWebroot/' . $asset->getPath();

        (new ServeFileDownload())->serve(
            $fullPath,
            $this->request,
            $asset->getMimeType()
        );

        // We should never reach this. Above statement should exit
        exit();
    }
}

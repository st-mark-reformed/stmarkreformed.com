<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModelFactory;
use App\Http\PageBuilder\Shared\AudioPlayer\RenderAudioPlayerFromContentModel;
use App\Http\Pagination\Pagination;
use App\Http\Pagination\RenderPagination;
use App\Http\Response\Members\InternalMedia\MediaResults;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as TwigEnvironment;

class InternalMediaResponderFactory
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RenderPagination $renderPagination,
        private ResponseFactoryInterface $responseFactory,
        private AudioPlayerContentModelFactory $playerModelFactory,
        private RenderAudioPlayerFromContentModel $renderAudioPlayer,
    ) {
    }

    public function make(
        MediaResults $results,
        Pagination $pagination,
        ServerRequestInterface $request,
    ): InternalMediaResponderContract {
        if (! $results->hasEntries() && $pagination->currentPage() > 1) {
            return new RespondWithNotFound(request: $request);
        }

        if (! $results->hasEntries()) {
            return new RespondWithNoResults(
                twig: $this->twig,
                heroFactory: $this->heroFactory,
                responseFactory: $this->responseFactory,
            );
        }

        return new RespondWithResults(
            twig: $this->twig,
            heroFactory: $this->heroFactory,
            responseFactory: $this->responseFactory,
            renderPagination: $this->renderPagination,
            renderAudioPlayer: $this->renderAudioPlayer,
            playerModelFactory: $this->playerModelFactory,
        );
    }
}

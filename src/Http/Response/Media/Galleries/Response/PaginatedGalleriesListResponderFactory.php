<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Pagination\RenderPagination;
use App\Http\Response\Media\Galleries\GalleryResults;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as TwigEnvironment;

class PaginatedGalleriesListResponderFactory
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RenderPagination $renderPagination,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function make(
        GalleryResults $galleryResults,
        ServerRequestInterface $request,
    ): PaginatedGalleriesListResponderContract {
        if (! $galleryResults->hasEntries()) {
            return new RespondWithNotFound(request: $request);
        }

        return new RespondWithResults(
            twig: $this->twig,
            heroFactory: $this->heroFactory,
            responseFactory: $this->responseFactory,
            renderPagination: $this->renderPagination,
        );
    }
}

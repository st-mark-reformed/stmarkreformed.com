<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources\Response;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Pagination\Pagination;
use App\Http\Pagination\RenderPagination;
use App\Http\Response\Media\Resources\ResourceResults;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as TwigEnvironment;

class PaginatedResourcesResponderFactory
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RenderPagination $renderPagination,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function make(
        Pagination $pagination,
        ResourceResults $results,
        ServerRequestInterface $request,
    ): PaginatedResourcesResponderContract {
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
        );
    }
}

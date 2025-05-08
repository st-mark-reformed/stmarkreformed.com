<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Resources\Response\PaginatedResourcesResponderFactory;
use App\Http\Shared\PageNumberFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;

class PaginatedResourcesListAction
{
    private const PER_PAGE = 12;

    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/resources[/page/{pageNum:\d+}]',
            self::class,
        );
    }

    public function __construct(
        private PageNumberFactory $pageNumberFactory,
        private RetrieveResources $retrieveResources,
        private PaginatedResourcesResponderFactory $responderFactory,
    ) {
    }

    /**
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $pageNum = $this->pageNumberFactory->fromRequest(
            request: $request,
        );

        $pagination = (new Pagination())
            ->withBase(val: '/resources')
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $pageNum)
            ->withQueryStringFromArray(val: $request->getQueryParams());

        $results = $this->retrieveResources->retrieve(pagination: $pagination);

        $pagination = $pagination->withTotalResults(
            val: $results->totalResults(),
        );

        return $this->responderFactory->make(
            results: $results,
            request: $request,
            pagination: $pagination,
        )->respond(
            results: $results,
            pagination: $pagination,
        );
    }
}

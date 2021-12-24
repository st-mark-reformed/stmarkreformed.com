<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia;

use App\Http\Pagination\Pagination;
use App\Http\Response\Members\InternalMedia\Response\InternalMediaResponderFactory;
use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use App\Http\Shared\PageNumberFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;

class InternalMediaAction
{
    private const PER_PAGE = 25;

    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector
            ->get(
                '/members/internal-media[/page/{pageNum:\d+}]',
                self::class,
            )
            ->setArgument(
                'pageTitle',
                'Log in to view the members area'
            )
            ->add(RequireLogInMiddleware::class);
    }

    public function __construct(
        private RetrieveMedia $retrieveMedia,
        private PageNumberFactory $pageNumberFactory,
        private InternalMediaResponderFactory $responderFactory,
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
            ->withBase(val: '/members/internal-media')
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $pageNum)
            ->withQueryStringFromArray(val: $request->getQueryParams());

        $results = $this->retrieveMedia->retrieve(pagination: $pagination);

        $pagination = $pagination->withTotalResults(
            val: $results->totalResults(),
        );

        return $this->responderFactory->make(
            request: $request,
            results: $results,
            pagination: $pagination,
        )->respond(
            results: $results,
            pagination: $pagination,
        );
    }
}

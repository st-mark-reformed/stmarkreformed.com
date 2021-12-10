<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList;

use App\Http\Pagination\Pagination;
use App\Http\Response\News\NewsList\Response\PaginatedNewsListResponderFactory;
use App\Http\Shared\PageNumberFactory;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use yii\base\InvalidConfigException;

class PastorsPageListAction
{
    private const PER_PAGE = 12;

    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/pastors-page[/page/{pageNum:\d+}]',
            self::class,
        );
    }

    public function __construct(
        private PageNumberFactory $pageNumberFactory,
        private RetrieveNewsItems $retrieveNewsItems,
        private PaginatedNewsListResponderFactory $responderFactory,
    ) {
    }

    /**
     * @throws HttpNotFoundException
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $pageNum = $this->pageNumberFactory->fromRequest(
            request: $request,
        );

        $pagination = (new Pagination())
            ->withBase(val: '/pastors-page')
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $pageNum)
            ->withQueryStringFromArray(val: $request->getQueryParams());

        $results = $this->retrieveNewsItems->retrieve(
            pagination: $pagination,
            section: 'pastorsPage',
        );

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
            pageTitle: "Pastor's Page",
        );
    }
}

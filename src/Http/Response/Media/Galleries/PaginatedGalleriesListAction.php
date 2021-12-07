<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Galleries\Response\PaginatedGalleriesListResponderFactory;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use yii\base\InvalidConfigException;

class PaginatedGalleriesListAction
{
    private const PER_PAGE = 12;

    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/media/galleries[/page/{pageNum:\d+}]',
            self::class,
        );
    }

    public function __construct(
        private PageNumberFactory $pageNumberFactory,
        private RetrieveGalleries $retrieveGalleries,
        private PaginatedGalleriesListResponderFactory $responderFactory,
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
            ->withBase(val: '/media/galleries')
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $pageNum)
            ->withQueryStringFromArray(val: $request->getQueryParams());

        $galleryResults = $this->retrieveGalleries->retrieve(
            pagination: $pagination,
        );

        $pagination = $pagination->withTotalResults(
            val: $galleryResults->totalResults(),
        );

        return $this->responderFactory->make(
            request: $request,
            galleryResults: $galleryResults,
        )->respond(
            pagination: $pagination,
            galleryResults: $galleryResults,
        );
    }
}

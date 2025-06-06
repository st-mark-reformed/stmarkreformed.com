<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages;

use App\Http\Pagination\Pagination;
use App\Messages\MessagesApi;
use craft\elements\Entry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

class ApiPaginatedMessagesSearchAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/api/media/messages/search',
            self::class,
        );
    }

    public function __construct(
        private MessagesApi $messagesApi,
        private GenerateMessagesPagesForRedis $generate,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $params = Params::fromRequest(request: $request);

        $results = $this->messagesApi->retrieveMessages(
            params: $params->toMessageRetrievalParams(),
        );

        $pagination = (new Pagination())
            ->withPerPage($params->perPage())
            ->withCurrentPage($params->page())
            ->withTotalResults($results->absoluteTotal());

        $response->getBody()->write(json_encode([
            'currentPage' => $pagination->currentPage(),
            'perPage' => $pagination->perPage(),
            'totalResults' => $pagination->totalResults(),
            'totalPages' => $pagination->totalPages(),
            'pagesArray' => $pagination->pagesArray(),
            'prevPageLink' => $pagination->prevPageLink(),
            'nextPageLink' => $pagination->nextPageLink(),
            'firstPageLink' => $pagination->firstPageLink(),
            'lastPageLink' => $pagination->lastPageLink(),
            'entries' => $results->map(function (Entry $entry) {
                return $this->generate->createJsonArrayFromEntry($entry);
            }),
        ]));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

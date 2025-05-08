<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Messages\Response\ResponderFactory;
use App\Messages\MessagesApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

class PaginatedMessagesListAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/media/messages',
            self::class,
        );
    }

    public function __construct(
        private MessagesApi $messagesApi,
        private ResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $params = Params::fromRequest(request: $request);

        $result = $this->messagesApi->retrieveMessages(
            params: $params->toMessageRetrievalParams(),
        );

        $pagination = (new Pagination())
            ->withBase('/media/messages')
            ->withPerPage($params->perPage())
            ->withCurrentPage($params->page())
            ->withQueryStringBased(true)
            ->withTotalResults($result->absoluteTotal())
            ->withQueryStringFromArray($request->getQueryParams());

        return $this->responderFactory->make(result: $result)->respond(
            params: $params,
            result: $result,
            pagination: $pagination,
        );
    }
}

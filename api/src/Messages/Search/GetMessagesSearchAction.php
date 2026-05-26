<?php

declare(strict_types=1);

namespace App\Messages\Search;

use App\Messages\Generate\MessageEntryJsonFactory;
use App\Messages\Message;
use App\Pagination\Pagination;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function json_encode;

readonly class GetMessagesSearchAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/api/media/messages/search',
            self::class,
        );
    }

    public function __construct(
        private SearchMessages $searchMessages,
        private MessageEntryJsonFactory $entryFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $params = SearchMessagesParams::fromRequest(request: $request);

        $matches = $this->searchMessages->find(params: $params);

        $pagination = new Pagination()
            ->withPerPage(val: $params->perPage)
            ->withCurrentPage(val: $params->page)
            ->withTotalResults(val: $matches->count());

        $pageMessages = $matches->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        $entries = $pageMessages->map(
            callback: fn (Message $message): array => $this->entryFactory->create(
                message: $message,
            ),
        );

        $response->getBody()->write((string) json_encode([
            'currentPage' => $pagination->currentPage(),
            'perPage' => $pagination->perPage(),
            'totalResults' => $pagination->totalResults(),
            'totalPages' => $pagination->totalPages(),
            'pagesArray' => $pagination->pagesArray(),
            'prevPageLink' => $pagination->prevPageLink(),
            'nextPageLink' => $pagination->nextPageLink(),
            'firstPageLink' => $pagination->firstPageLink(),
            'lastPageLink' => $pagination->lastPageLink(),
            'entries' => $entries,
        ]));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

<?php

declare(strict_types=1);

namespace App\MailingLists\Admin;

use App\Auth\RequireEditMailingListsRoleMiddleware;
use App\MailingLists\MailingList;
use App\MailingLists\MailingListsRepository;
use App\Pagination\Pagination;
use App\RespondWithJson;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function is_int;
use function is_string;
use function max;
use function stripos;
use function trim;

readonly class GetMailingListsListAction
{
    private const int PER_PAGE = 100;

    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/mailing-lists',
            self::class,
        )->add(RequireEditMailingListsRoleMiddleware::class);
    }

    public function __construct(
        private MailingListsRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $keyword = $this->keywordFromRequest(request: $request);

        $mailingLists = $this->repository->findAll();

        if ($keyword !== '') {
            $mailingLists = $mailingLists->filter(
                callback: fn (MailingList $mailingList): bool => $this->matchesKeyword(
                    mailingList: $mailingList,
                    keyword: $keyword,
                ),
            );
        }

        $pagination = new Pagination()
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $this->pageFromRequest(request: $request))
            ->withTotalResults(val: $mailingLists->count());

        $pageItems = $mailingLists->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        return new RespondWithJson(
            entity: new PaginatedMailingLists(
                mailingLists: $pageItems,
                pagination: $pagination,
            ),
            factory: $this->factory,
        )->respond();
    }

    private function matchesKeyword(
        MailingList $mailingList,
        string $keyword,
    ): bool {
        return stripos($mailingList->listName, $keyword) !== false
            || stripos($mailingList->listAddress, $keyword) !== false;
    }

    private function pageFromRequest(ServerRequestInterface $request): int
    {
        $page = $request->getQueryParams()['page'] ?? null;

        if (is_int($page)) {
            return max(1, $page);
        }

        if (is_string($page) && $page !== '') {
            return max(1, (int) $page);
        }

        return 1;
    }

    private function keywordFromRequest(ServerRequestInterface $request): string
    {
        $keyword = $request->getQueryParams()['keyword'] ?? null;

        return is_string($keyword) ? trim($keyword) : '';
    }
}

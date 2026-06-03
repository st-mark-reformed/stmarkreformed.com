<?php

declare(strict_types=1);

namespace App\PastorsPage\Admin;

use App\Auth\RequireEditPastorsPageRoleMiddleware;
use App\Pagination\Pagination;
use App\PastorsPage\PastorsPageItem;
use App\PastorsPage\PastorsPageRepository;
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

readonly class GetPastorsPageListAction
{
    private const int PER_PAGE = 100;

    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/pastors-page',
            self::class,
        )->add(RequireEditPastorsPageRoleMiddleware::class);
    }

    public function __construct(
        private PastorsPageRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $keyword = $this->keywordFromRequest(request: $request);

        $pastorsPageItems = $this->repository->findAll();

        if ($keyword !== '') {
            $pastorsPageItems = $pastorsPageItems->filter(
                callback: fn (
                    PastorsPageItem $pastorsPageItem,
                ): bool => $this->matchesKeyword(
                    pastorsPageItem: $pastorsPageItem,
                    keyword: $keyword,
                ),
            );
        }

        $pagination = new Pagination()
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $this->pageFromRequest(request: $request))
            ->withTotalResults(val: $pastorsPageItems->count());

        $pageItems = $pastorsPageItems->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        return new RespondWithJson(
            entity: new PaginatedPastorsPage(
                pastorsPageItems: $pageItems,
                pagination: $pagination,
            ),
            factory: $this->factory,
        )->respond();
    }

    private function matchesKeyword(
        PastorsPageItem $pastorsPageItem,
        string $keyword,
    ): bool {
        return stripos($pastorsPageItem->title, $keyword) !== false
            || stripos($pastorsPageItem->heading, $keyword) !== false
            || stripos($pastorsPageItem->subheading, $keyword) !== false;
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

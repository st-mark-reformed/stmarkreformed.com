<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Admin;

use App\Auth\RequireEditHymnsOfTheMonthRoleMiddleware;
use App\HymnsOfTheMonth\HymnOfTheMonthItem;
use App\HymnsOfTheMonth\HymnsOfTheMonthRepository;
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

readonly class GetHymnsOfTheMonthListAction
{
    private const int PER_PAGE = 100;

    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/hymns-of-the-month',
            self::class,
        )->add(RequireEditHymnsOfTheMonthRoleMiddleware::class);
    }

    public function __construct(
        private HymnsOfTheMonthRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $keyword = $this->keywordFromRequest(request: $request);

        $hymnOfTheMonthItems = $this->repository->findAll();

        if ($keyword !== '') {
            $hymnOfTheMonthItems = $hymnOfTheMonthItems->filter(
                callback: fn (
                    HymnOfTheMonthItem $hymnOfTheMonthItem,
                ): bool => $this->matchesKeyword(
                    hymnOfTheMonthItem: $hymnOfTheMonthItem,
                    keyword: $keyword,
                ),
            );
        }

        $pagination = new Pagination()
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $this->pageFromRequest(request: $request))
            ->withTotalResults(val: $hymnOfTheMonthItems->count());

        $pageItems = $hymnOfTheMonthItems->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        return new RespondWithJson(
            entity: new PaginatedHymnsOfTheMonth(
                hymnOfTheMonthItems: $pageItems,
                pagination: $pagination,
            ),
            factory: $this->factory,
        )->respond();
    }

    private function matchesKeyword(
        HymnOfTheMonthItem $hymnOfTheMonthItem,
        string $keyword,
    ): bool {
        return stripos($hymnOfTheMonthItem->title, $keyword) !== false
            || stripos($hymnOfTheMonthItem->hymnPsalmName, $keyword) !== false;
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

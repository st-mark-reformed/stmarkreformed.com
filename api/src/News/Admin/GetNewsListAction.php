<?php

declare(strict_types=1);

namespace App\News\Admin;

use App\Auth\RequireEditNewsRoleMiddleware;
use App\News\NewsItem;
use App\News\NewsRepository;
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

readonly class GetNewsListAction
{
    private const int PER_PAGE = 100;

    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/news',
            self::class,
        )->add(RequireEditNewsRoleMiddleware::class);
    }

    public function __construct(
        private NewsRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $keyword = $this->keywordFromRequest(request: $request);

        $newsItems = $this->repository->findAll();

        if ($keyword !== '') {
            $newsItems = $newsItems->filter(
                callback: fn (NewsItem $newsItem): bool => $this->matchesKeyword(
                    newsItem: $newsItem,
                    keyword: $keyword,
                ),
            );
        }

        $pagination = new Pagination()
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $this->pageFromRequest(request: $request))
            ->withTotalResults(val: $newsItems->count());

        $pageItems = $newsItems->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        return new RespondWithJson(
            entity: new PaginatedNews(
                newsItems: $pageItems,
                pagination: $pagination,
            ),
            factory: $this->factory,
        )->respond();
    }

    private function matchesKeyword(NewsItem $newsItem, string $keyword): bool
    {
        return stripos($newsItem->title, $keyword) !== false
            || stripos($newsItem->heading, $keyword) !== false
            || stripos($newsItem->subheading, $keyword) !== false;
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

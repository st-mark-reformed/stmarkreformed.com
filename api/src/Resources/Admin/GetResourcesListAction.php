<?php

declare(strict_types=1);

namespace App\Resources\Admin;

use App\Auth\RequireEditResourcesRoleMiddleware;
use App\Pagination\Pagination;
use App\Resources\ResourceItem;
use App\Resources\ResourcesRepository;
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

readonly class GetResourcesListAction
{
    private const int PER_PAGE = 100;

    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/resources',
            self::class,
        )->add(RequireEditResourcesRoleMiddleware::class);
    }

    public function __construct(
        private ResourcesRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $keyword = $this->keywordFromRequest(request: $request);

        $resourceItems = $this->repository->findAll();

        if ($keyword !== '') {
            $resourceItems = $resourceItems->filter(
                callback: fn (ResourceItem $resourceItem): bool => $this->matchesKeyword(
                    resourceItem: $resourceItem,
                    keyword: $keyword,
                ),
            );
        }

        $pagination = new Pagination()
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $this->pageFromRequest(request: $request))
            ->withTotalResults(val: $resourceItems->count());

        $pageItems = $resourceItems->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        return new RespondWithJson(
            entity: new PaginatedResources(
                resourceItems: $pageItems,
                pagination: $pagination,
            ),
            factory: $this->factory,
        )->respond();
    }

    private function matchesKeyword(
        ResourceItem $resourceItem,
        string $keyword,
    ): bool {
        return stripos($resourceItem->title, $keyword) !== false;
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

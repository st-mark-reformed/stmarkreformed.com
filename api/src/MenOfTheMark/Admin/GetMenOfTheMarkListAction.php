<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Admin;

use App\Auth\RequireEditMenOfTheMarkRoleMiddleware;
use App\MenOfTheMark\MenOfTheMarkItem;
use App\MenOfTheMark\MenOfTheMarkRepository;
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

readonly class GetMenOfTheMarkListAction
{
    private const int PER_PAGE = 100;

    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/men-of-the-mark',
            self::class,
        )->add(RequireEditMenOfTheMarkRoleMiddleware::class);
    }

    public function __construct(
        private MenOfTheMarkRepository $repository,
        private ResponseFactoryInterface $factory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $keyword = $this->keywordFromRequest(request: $request);

        $items = $this->repository->findAll();

        if ($keyword !== '') {
            $items = $items->filter(
                callback: static fn (MenOfTheMarkItem $item): bool => stripos(
                    $item->title,
                    $keyword,
                ) !== false,
            );
        }

        $pagination = new Pagination()
            ->withPerPage(val: self::PER_PAGE)
            ->withCurrentPage(val: $this->pageFromRequest(request: $request))
            ->withTotalResults(val: $items->count());

        $pageItems = $items->sliceToPage(
            page: $pagination->currentPage(),
            perPage: $pagination->perPage(),
        );

        return new RespondWithJson(
            entity: new PaginatedMenOfTheMark(
                items: $pageItems,
                pagination: $pagination,
            ),
            factory: $this->factory,
        )->respond();
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

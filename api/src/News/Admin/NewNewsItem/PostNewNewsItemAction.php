<?php

declare(strict_types=1);

namespace App\News\Admin\NewNewsItem;

use App\Auth\RequireEditNewsRoleMiddleware;
use App\News\NewsRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostNewNewsItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/news/new',
            self::class,
        )->add(RequireEditNewsRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewNewsItemFactory $newNewsItemFactory,
        private NewsRepository $newsRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newNewsItem = $this->newNewsItemFactory->createFromRequest(
            request: $request,
        );

        $result = $this->newsRepository->create(newsItem: $newNewsItem);

        return $this->responder->respond(result: $result);
    }
}

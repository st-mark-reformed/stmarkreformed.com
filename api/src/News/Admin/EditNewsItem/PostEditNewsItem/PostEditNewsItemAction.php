<?php

declare(strict_types=1);

namespace App\News\Admin\EditNewsItem\PostEditNewsItem;

use App\Auth\RequireEditNewsRoleMiddleware;
use App\News\NewsRepository;
use App\Result\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostEditNewsItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->patch(
            '/admin/news/edit/{newsId}',
            self::class,
        )->add(RequireEditNewsRoleMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private NewsItemFactory $newsItemFactory,
        private NewsRepository $newsRepository,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newsItem = $this->newsItemFactory->createFromRequest(
            request: $request,
        );

        $result = $this->newsRepository->persist(newsItem: $newsItem);

        return $this->responder->respond(result: $result);
    }
}

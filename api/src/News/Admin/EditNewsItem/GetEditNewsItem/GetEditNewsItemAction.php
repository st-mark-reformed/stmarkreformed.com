<?php

declare(strict_types=1);

namespace App\News\Admin\EditNewsItem\GetEditNewsItem;

use App\Auth\RequireEditNewsRoleMiddleware;
use App\News\NewsRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetEditNewsItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/news/edit/{newsId}',
            self::class,
        )->add(RequireEditNewsRoleMiddleware::class);
    }

    public function __construct(
        private NewsRepository $repository,
        private EditNewsItemResponderFactory $responderFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $newsId = $request->attributes->getString(name: 'newsId');

        $newsItemResult = $this->repository->findById(id: $newsId);

        $responder = $this->responderFactory->create(result: $newsItemResult);

        return $responder->respond();
    }
}

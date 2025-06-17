<?php

declare(strict_types=1);

namespace App\Messages\Series;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use App\Persistence\ResultResponder;
use App\Persistence\UuidCollectionFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

readonly class DeleteMessageSeriesCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->delete(
            '/cms/entries/messages/series',
            self::class,
        )
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private MessageSeriesRepository $repository,
        private UuidCollectionFactory $requestDataFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $ids = $this->requestDataFactory->fromServerRequest(
            $request,
        );

        $result = $this->repository->deleteIds($ids);

        return $this->responder->respond($result);
    }
}

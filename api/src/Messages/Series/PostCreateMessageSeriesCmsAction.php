<?php

declare(strict_types=1);

namespace App\Messages\Series;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use App\Persistence\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

readonly class PostCreateMessageSeriesCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/cms/entries/messages/series',
            self::class,
        )
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private MessageSeriesRepository $repository,
        private MessageSeriesEntityFactory $messageSeriesEntityFactory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $newSeries = $this->messageSeriesEntityFactory->fromServerRequest(
            $request,
        );

        $result = $this->repository->createAndPersist(
            $newSeries,
        );

        return $this->responder->respond($result);
    }
}

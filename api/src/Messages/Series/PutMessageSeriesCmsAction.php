<?php

declare(strict_types=1);

namespace App\Messages\Series;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use App\Persistence\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use Throwable;

use function assert;
use function is_string;

readonly class PutMessageSeriesCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->put(
            '/cms/entries/messages/series/{id}',
            self::class,
        )
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private MessageSeriesRepository $repository,
        private MessageSeriesEntityFactory $entityFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $idString = $request->getAttribute('id');
        assert(is_string($idString));

        try {
            $id = Uuid::fromString($idString);
        } catch (Throwable) {
            $id = Uuid::uuid6();
        }

        $series = $this->repository->findById($id);

        if ($series === null) {
            return $response->withStatus(404)->withHeader(
                'Content-type',
                'application/json',
            );
        }

        $updatedSeries = $this->entityFactory->fromServerRequest(
            $request,
        )->withId($id);

        $result = $this->repository->persist($updatedSeries);

        return $this->responder->respond($result);
    }
}

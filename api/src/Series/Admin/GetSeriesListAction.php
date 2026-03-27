<?php

declare(strict_types=1);

namespace App\Series\Admin;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\Series\SeriesRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function json_encode;

readonly class GetSeriesListAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/messages/series',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(private SeriesRepository $repository)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $series = $this->repository->findAll();

        $response->getBody()->write((string) json_encode(
            $series->asArray(),
        ));

        return $response->withHeader('Content-type', 'application/json');
    }
}

<?php

declare(strict_types=1);

namespace App\InternalSeries\Admin;

use App\Auth\RequireEditMessagesRoleMiddleware;
use App\InternalSeries\InternalSeriesRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;

use function json_encode;

readonly class GetInternalSeriesListAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/internal-messages/series',
            self::class,
        )->add(RequireEditMessagesRoleMiddleware::class);
    }

    public function __construct(private InternalSeriesRepository $repository)
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

<?php

declare(strict_types=1);

namespace App\Messages\Series;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

readonly class GetAllMessageSeriesCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/cms/entries/messages/series', self::class)
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(private MessageSeriesRepository $repository)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $series = $this->repository->findAll();

        $response->getBody()->write(
            (string) json_encode($series->asScalar()),
        );

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

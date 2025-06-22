<?php

declare(strict_types=1);

namespace App\Messages;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

readonly class GetAllMessagesCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get('/cms/entries/messages', self::class)
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(private MessageRepository $repository)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $messages = $this->repository->findAll();

        $response->getBody()->write(
            (string) json_encode($messages->asScalar()),
        );

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

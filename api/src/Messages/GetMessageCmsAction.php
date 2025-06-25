<?php

declare(strict_types=1);

namespace App\Messages;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use Throwable;

use function assert;
use function is_string;
use function json_encode;

readonly class GetMessageCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/cms/entries/messages/{id}',
            self::class,
        )
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
        $idString = $request->getAttribute('id');
        assert(is_string($idString));

        try {
            $id = Uuid::fromString($idString);
        } catch (Throwable) {
            $id = Uuid::uuid6();
        }

        $message = $this->repository->findById($id);

        if ($message === null) {
            return $response->withStatus(404)->withHeader(
                'Content-type',
                'application/json',
            );
        }

        $response->getBody()->write(
            (string) json_encode($message->asScalar()),
        );

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

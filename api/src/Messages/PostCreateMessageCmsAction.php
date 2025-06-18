<?php

declare(strict_types=1);

namespace App\Messages;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use App\Persistence\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

readonly class PostCreateMessageCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post('/cms/entries/messages', self::class)
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private ResultResponder $responder,
        private MessageRepository $repository,
        private MessageEntityFactory $entityFactory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $newMessage = $this->entityFactory->fromServerRequest(
            $request,
        );

        $result = $this->repository->createAndPersist($newMessage);

        return $this->responder->respond($result);
    }
}

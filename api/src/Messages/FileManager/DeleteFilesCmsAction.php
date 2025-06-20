<?php

declare(strict_types=1);

namespace App\Messages\FileManager;

use App\Authentication\RequireCmsAccessRoleMiddleware;
use App\Persistence\ResultResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

readonly class DeleteFilesCmsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->delete(
            '/cms/entries/messages/files',
            self::class,
        )
            ->add(RequireCmsAccessRoleMiddleware::class)
            ->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private FileRepository $repository,
        private ResultResponder $responder,
        private FileNameCollectionFactory $requestDataFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $names = $this->requestDataFactory->fromServerRequest(
            $request,
        );

        $result = $this->repository->deleteNames($names);

        return $this->responder->respond($result);
    }
}

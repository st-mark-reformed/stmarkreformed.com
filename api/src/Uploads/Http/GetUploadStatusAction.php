<?php

declare(strict_types=1);

namespace App\Uploads\Http;

use App\Auth\RequireAnyEditRoleMiddleware;
use App\RespondWithJson;
use App\Uploads\UploadRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class GetUploadStatusAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/uploads/{uploadId}',
            self::class,
        )->add(RequireAnyEditRoleMiddleware::class);
    }

    public function __construct(
        private UploadRepository $uploadRepository,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $uploadId = $request->attributes->getString(name: 'uploadId');

        $session = $this->uploadRepository->readSession(uploadId: $uploadId);

        return new RespondWithJson(
            entity: new UploadProgressResponse(
                uploadId: $uploadId,
                receivedChunks: $this->uploadRepository
                    ->receivedChunks(uploadId: $uploadId)
                    ->asArray(),
                totalChunks: $session->totalChunks ?? 0,
            ),
            factory: $this->responseFactory,
        )->respond();
    }
}

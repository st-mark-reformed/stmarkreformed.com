<?php

declare(strict_types=1);

namespace App\Uploads\Http;

use App\Auth\RequireAnyEditRoleMiddleware;
use App\RespondWithJson;
use App\Uploads\UploadAcceptType;
use App\Uploads\UploadRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostInitiateUploadAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/uploads',
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
        $session = $this->uploadRepository->initiate(
            fileName: $request->parsedBody->getString(name: 'fileName'),
            totalSize: $request->parsedBody->getInt(name: 'totalSize'),
            chunkSize: $request->parsedBody->getInt(name: 'chunkSize'),
            totalChunks: $request->parsedBody->getInt(name: 'totalChunks'),
            acceptType: UploadAcceptType::fromString(
                $request->parsedBody->findString(name: 'acceptType') ?? 'any',
            ),
        );

        return new RespondWithJson(
            entity: new UploadProgressResponse(
                uploadId: $session->uploadId,
                receivedChunks: [],
                totalChunks: $session->totalChunks,
            ),
            factory: $this->responseFactory,
        )->respond();
    }
}

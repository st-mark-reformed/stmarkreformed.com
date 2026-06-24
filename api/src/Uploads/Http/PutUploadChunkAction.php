<?php

declare(strict_types=1);

namespace App\Uploads\Http;

use App\Auth\RequireAnyEditRoleMiddleware;
use App\RespondWithJson;
use App\Result\ResultResponder;
use App\Uploads\UploadRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PutUploadChunkAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->put(
            '/admin/uploads/{uploadId}/chunk/{index}',
            self::class,
        )->add(RequireAnyEditRoleMiddleware::class);
    }

    public function __construct(
        private UploadRepository $uploadRepository,
        private ResultResponder $resultResponder,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $uploadId = $request->attributes->getString(name: 'uploadId');

        $result = $this->uploadRepository->writeChunk(
            uploadId: $uploadId,
            index: $request->attributes->getInt(name: 'index'),
            base64Data: $request->parsedBody->getString(name: 'data'),
        );

        if (! $result->success) {
            return $this->resultResponder->respond(result: $result);
        }

        return new RespondWithJson(
            entity: new UploadProgressResponse(
                uploadId: $uploadId,
                receivedChunks: $this->uploadRepository
                    ->receivedChunks(uploadId: $uploadId)
                    ->asArray(),
                totalChunks: $this->uploadRepository
                    ->readSession(uploadId: $uploadId)->totalChunks ?? 0,
            ),
            factory: $this->responseFactory,
        )->respond();
    }
}

<?php

declare(strict_types=1);

namespace App\Uploads\Http;

use App\Auth\RequireAnyEditRoleMiddleware;
use App\Result\ResultResponder;
use App\Uploads\UploadRepository;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class PostCompleteUploadAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/uploads/{uploadId}/complete',
            self::class,
        )->add(RequireAnyEditRoleMiddleware::class);
    }

    public function __construct(
        private UploadRepository $uploadRepository,
        private ResultResponder $responder,
    ) {
    }

    public function __invoke(ServerRequest $request): ResponseInterface
    {
        $result = $this->uploadRepository->complete(
            uploadId: $request->attributes->getString(name: 'uploadId'),
        );

        return $this->responder->respond(result: $result);
    }
}

<?php

declare(strict_types=1);

namespace App\Queue;

use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

use const JSON_PRETTY_PRINT;

readonly class PostClearFailedQueueItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/queue/failed/clear',
            self::class,
        )->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private FailedQueueItemClearer $failedQueueItemClearer,
    ) {
    }

    public function __invoke(
        ServerRequest $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $key = $request->parsedBody->getString('key');

        $response->getBody()->write((string) json_encode(
            ['wasSuccessful' => $this->failedQueueItemClearer->clear($key)],
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Queue;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

use const JSON_PRETTY_PRINT;

readonly class PostClearAllFailedQueueItemsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/queue/failed/clear-all',
            self::class,
        )->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(
        private FailedQueueItemClearer $failedQueueItemClearer,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write((string) json_encode(
            ['cleared' => $this->failedQueueItemClearer->clearAll()],
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

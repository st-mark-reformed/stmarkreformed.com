<?php

declare(strict_types=1);

namespace App\Queue;

use BuzzingPixel\Queue\QueueHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

use const JSON_PRETTY_PRINT;

readonly class GetAdminQueueStatusAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/queue/status',
            self::class,
        )->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(private QueueHandler $queueHandler)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write((string) json_encode(
            [
                'enqueued' => $this->queueHandler->getTotalItemsInQueue(),
                'failed' => $this->queueHandler->getFailedItems()->count(),
            ],
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

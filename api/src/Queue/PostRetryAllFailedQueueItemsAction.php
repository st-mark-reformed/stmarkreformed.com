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

readonly class PostRetryAllFailedQueueItemsAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/queue/failed/retry-all',
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
        $retried = 0;

        foreach ($this->queueHandler->getFailedItems()->items as $item) {
            // Items already retried were re-enqueued once; retrying them
            // again would run the same job twice
            if ($item->retried) {
                continue;
            }

            if (! $this->queueHandler->retryFailedItemByKey($item->key)->wasSuccessful) {
                continue;
            }

            $retried++;
        }

        $response->getBody()->write((string) json_encode(
            ['retried' => $retried],
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Queue;

use BuzzingPixel\Queue\QueueHandler;
use Psr\Http\Message\ResponseInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\Request\ServerRequest;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

use const JSON_PRETTY_PRINT;

readonly class PostRetryFailedQueueItemAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->post(
            '/admin/queue/failed/retry',
            self::class,
        )->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(private QueueHandler $queueHandler)
    {
    }

    public function __invoke(
        ServerRequest $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $key = $request->parsedBody->getString('key');

        $result = $this->queueHandler->retryFailedItemByKey($key);

        $response->getBody()->write((string) json_encode(
            $result->asArray(),
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

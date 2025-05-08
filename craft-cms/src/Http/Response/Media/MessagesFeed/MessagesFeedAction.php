<?php

declare(strict_types=1);

namespace App\Http\Response\Media\MessagesFeed;

use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class MessagesFeedAction
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private MessagesRssFeedFactory $messagesRssFeedFactory,
    ) {
    }

    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/media/messages/feed',
            self::class,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function __invoke(): ResponseInterface
    {
        $feed = $this->messagesRssFeedFactory->make();

        $response = $this->responseFactory->createResponse()
            ->withHeader('EnableStaticCache', 'true')
            ->withHeader('Content-Type', 'text/xml');

        $response->getBody()->write((string) $feed->saveXML());

        return $response;
    }
}

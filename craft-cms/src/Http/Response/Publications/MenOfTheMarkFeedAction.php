<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

class MenOfTheMarkFeedAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/publications/men-of-the-mark/rss',
            self::class,
        );
    }

    public function __construct(
        private MenOfTheMarkFeedFactory $feedFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $feed = $this->feedFactory->make();

        $response = $response
            ->withHeader('EnableStaticCache', 'true')
            ->withHeader('Content-Type', 'text/xml');

        $response->getBody()->write((string) $feed->saveXML());

        return $response;
    }
}

<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

// phpcs:disable SlevomatCodingStandard.Functions.StaticClosure.ClosureNotStatic

return static function (App $app): void {
    // TODO: Remove this when we have real routes
    $app->get('/test', function () use (
        $app
    ): ResponseInterface {
        $container = $app->getContainer();

        assert($container instanceof ContainerInterface);

        $responseFactory = $container->get(ResponseFactoryInterface::class);

        assert(
            $responseFactory instanceof ResponseFactoryInterface
        );

        $response = $responseFactory->createResponse();

        $response->getBody()->write('hello world');

        return $response;
    });
};

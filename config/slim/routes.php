<?php

declare(strict_types=1);

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\PostSubmissionAction;
use Slim\App;

// phpcs:disable SlevomatCodingStandard.Functions.StaticClosure.ClosureNotStatic

return static function (App $app): void {
    // $app->get('/test', function () use (
    //     $app
    // ): ResponseInterface {
    //     $container = $app->getContainer();
    //
    //     assert($container instanceof ContainerInterface);
    //
    //     $responseFactory = $container->get(ResponseFactoryInterface::class);
    //
    //     assert(
    //         $responseFactory instanceof ResponseFactoryInterface
    //     );
    //
    //     $response = $responseFactory->createResponse();
    //
    //     $response->getBody()->write('hello world');
    //
    //     return $response;
    // });

    PostSubmissionAction::addRoute(routeCollector: $app);
};

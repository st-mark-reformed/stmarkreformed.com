<?php

declare(strict_types=1);

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\PostSubmissionAction;
use Config\Tinker;
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

    if ((bool) getenv('DEV_MODE')) {
        $app->get('/tinker', Tinker::class);
    }

    PostSubmissionAction::addRoute(routeCollector: $app);
};

<?php

declare(strict_types=1);

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\PostSubmissionAction;
use App\Http\Response\Calendar\GetCalendarAction;
use App\Http\Response\Calendar\GetCalendarIndexAction;
use App\Http\Response\Calendar\GetIcsAction;
use App\Http\Response\LogIn\PostLogInAction;
use App\Http\Response\Media\Galleries\PaginatedGalleriesListAction;
use App\Http\Response\Media\Messages\PaginatedMessagesListAction;
use App\Http\Response\Media\MessagesFeed\MessagesFeedAction;
use App\Http\Response\Media\Resources\PaginatedResourcesListAction;
use App\Http\Response\Members\HymnsOfTheMonth\FileDownload\FileDownloadAction;
use App\Http\Response\Members\HymnsOfTheMonth\HymnsOfTheMonthAction;
use App\Http\Response\Members\InternalMedia\DownloadAudio\DownloadAudioAction;
use App\Http\Response\Members\InternalMedia\InternalMediaAction;
use App\Http\Response\Members\MembersIndexAction;
use App\Http\Response\News\NewsList\PaginatedNewsListAction;
use App\Http\Response\News\NewsList\PastorsPageListAction;
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

    PaginatedMessagesListAction::addRoute(routeCollector: $app);

    MessagesFeedAction::addRoute(routeCollector: $app);

    PaginatedGalleriesListAction::addRoute(routeCollector: $app);

    PaginatedResourcesListAction::addRoute(routeCollector: $app);

    PaginatedNewsListAction::addRoute(routeCollector: $app);

    PastorsPageListAction::addRoute(routeCollector: $app);

    MembersIndexAction::addRoute(routeCollector: $app);

    PostLogInAction::addRoute(routeCollector: $app);

    InternalMediaAction::addRoute(routeCollector: $app);

    DownloadAudioAction::addRoute(routeCollector: $app);

    HymnsOfTheMonthAction::addRoute(routeCollector: $app);

    FileDownloadAction::addRoute(routeCollector: $app);

    GetCalendarIndexAction::addRoute(routeCollector: $app);

    GetCalendarAction::addRoute(routeCollector: $app);

    GetIcsAction::addRoute(routeCollector: $app);
};

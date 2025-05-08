<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\DownloadAudio;

use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use App\Http\Shared\FileDownload\ServeFileDownload;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use yii\base\InvalidConfigException;

class DownloadAudioAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector
            ->get(
                '/members/internal-audio/audio/{slug}',
                self::class,
            )
            ->setArgument(
                'pageTitle',
                'Log in to view the members area'
            )
            ->add(RequireLogInMiddleware::class);
    }

    public function __construct(
        private RetrieveResult $retrieveResult,
        private ServeFileDownload $serveFileDownload,
    ) {
    }

    /**
     * @throws HttpNotFoundException
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function __invoke(ServerRequestInterface $request): void
    {
        $slug = (string) $request->getAttribute('slug');

        $result = $this->retrieveResult->fromSlug(slug: $slug);

        if (! $result->hasResult()) {
            throw new HttpNotFoundException($request);
        }

        $this->serveFileDownload->serve(
            request: $request,
            fullServerPath: $result->pathOnServer(),
            mimeType: $result->mimeType(),
        );
    }
}

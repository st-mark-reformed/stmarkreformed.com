<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

class GetCalendarIndexAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get('/calendar', self::class);
    }

    /**
     * @throws Exception
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $currentTime = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
        );

        return $response->withStatus(303)->withHeader(
            'Location',
            '/calendar/' . $currentTime->format('Y/m'),
        );
    }
}

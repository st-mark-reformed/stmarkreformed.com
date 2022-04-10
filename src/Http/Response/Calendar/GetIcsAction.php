<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use App\Shared\ElementQueryFactories\CalendarEventQueryFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function mb_strlen;

class GetIcsAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get('/calendar/ics', self::class);
    }

    public function __construct(
        private CalendarEventQueryFactory $calendarEventQueryFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $query = $this->calendarEventQueryFactory->make()
            ->setCalendar('stMarkEvents')
            ->setLoadOccurrences(false);

        $exporter = new ExportCalendarToIcsCentralTime($query);

        $exportString = $exporter->output();

        $response = $response
            ->withHeader('Content-type', 'text/calendar; charset=utf-8')
            ->withHeader('Expires', '0')
            ->withHeader(
                'Cache-Control',
                'must-revalidate, post-check=0, pre-check=0',
            )
            ->withHeader('Pragma', 'public')
            ->withHeader('Content-Length', (string) mb_strlen($exportString));

        $response->getBody()->write($exportString);

        return $response;
    }
}

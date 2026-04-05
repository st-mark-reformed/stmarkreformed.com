<?php

declare(strict_types=1);

namespace App\Schedule\Admin;

use BuzzingPixel\Scheduler\PersistentScheduleItem;
use BuzzingPixel\Scheduler\ScheduleHandler;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;

use function json_encode;

use const JSON_PRETTY_PRINT;

readonly class GetAdminScheduleAction
{
    public static function applyRoute(ApplyRoutesEvent $routes): void
    {
        $routes->get(
            '/admin/schedule',
            self::class,
        )->add(RequireOauthTokenHeaderMiddleware::class);
    }

    public function __construct(private ScheduleHandler $scheduleHandler)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write((string) json_encode(
            $this->scheduleHandler->fetchSchedule()->map(
                static function (PersistentScheduleItem $item): array {
                    return [
                        'runEvery' => $item->runEvery->name,
                        'class' => $item->class,
                        'method' => $item->method,
                        'lastRunStartAt' => $item->lastRunStartAt?->setTimezone(
                            new DateTimeZone('US/Central'),
                        )->format(
                            'l, F j, g:i A',
                        ),
                        'lastRunEndAt' => $item->lastRunEndAt?->setTimezone(
                            new DateTimeZone('US/Central'),
                        )->format(
                            'l, F j, g:i A',
                        ),
                    ];
                },
            ),
            JSON_PRETTY_PRINT,
        ));

        return $response->withHeader(
            'Content-type',
            'application/json',
        );
    }
}

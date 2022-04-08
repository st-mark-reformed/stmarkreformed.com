<?php

declare(strict_types=1);

namespace App\Http\Response\Calendar;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use App\Shared\ElementQueryFactories\CalendarEventQueryFactory;
use Carbon\Carbon;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Solspace\Calendar\Elements\Event;
use Twig\Environment as TwigEnvironment;

use function assert;
use function mb_strlen;

class GetCalendarAction
{
    public static function addRoute(
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $routeCollector->get(
            '/calendar/{year:\d+}/{month:\d+}',
            self::class
        );
    }

    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private MonthDayFactory $monthDayFactory,
        private MonthRangeFactory $monthRangeFactory,
        private CalendarEventQueryFactory $calendarEventQueryFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $year = $request->getAttribute('year');

        $is4Dig = mb_strlen($year) === 4;

        $month = $request->getAttribute('month');

        $is2Dig = mb_strlen($month) === 2;

        if (! $is4Dig || ! $is2Dig) {
            throw new HttpNotFoundException($request);
        }

        $monthString = $year . '-' . $month;

        $firstDay = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $monthString . '-01 00:00:00',
            new DateTimeZone('US/Central'),
        );

        assert($firstDay instanceof DateTimeImmutable);

        $response = $response->withHeader(
            'EnableStaticCache',
            'true'
        );

        $monthRange = $this->monthRangeFactory->make(month: $monthString);

        $startDate = $monthRange->getStartDate();

        assert($startDate instanceof DateTimeImmutable);

        $startCarbon = Carbon::createFromInterface(
            $startDate->sub(new DateInterval('P1D')),
        );

        assert($startCarbon instanceof Carbon);

        $endDate = $monthRange->getEndDate();

        assert($endDate instanceof DateTimeImmutable);

        $endCarbon = Carbon::createFromInterface(
            $endDate->add(new DateInterval('P1D')),
        );

        assert($endCarbon instanceof Carbon);

        $events = new EventCollection(
            events: $this->calendarEventQueryFactory->make()
                ->setCalendar('stMarkEvents')
                ->setRangeStart($startCarbon)
                ->setRangeEnd($endCarbon)
                ->all(),
        );

        $monthDays = $this->monthDayFactory->create(
            events: $events,
            month: $monthString,
            monthRange: $monthRange,
        );

        $dateHeading = $firstDay->format('F Y');

        $monthEventsOnly = $events->filter(
            static function (Event $event) use ($monthString): bool {
                $startDate = $event->getStartDate();

                if ($startDate === null) {
                    return false;
                }

                return $startDate->format('Y-m') === $monthString;
            }
        );

        $prevMonth = $startDate->sub(new DateInterval('P3D'));

        $nextMonth = $endDate->add(new DateInterval('P3D'));

        $currentMonthLink = null;

        $currentMonth = new DateTimeImmutable(
            'now',
            new DateTimeZone('US/Central'),
        );

        $currentString = $currentMonth->format('Y-m');

        if ($currentString !== $monthString) {
            $currentMonthLink = '/calendar/' . $currentMonth->format(
                'Y/m',
            );
        }

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Calendar/Calendar.twig',
            [
                'meta' => new Meta('Calendar | ' . $dateHeading),
                'hero' => $this->heroFactory->createFromDefaults(
                    heroHeading: 'Events at St. Mark',
                ),
                'monthString' => $monthString,
                'dateHeading' => $dateHeading,
                'monthDays' => $monthDays,
                'monthEventsOnly' => $monthEventsOnly,
                'currentMonthLink' => $currentMonthLink,
                'prevMonthLink' => '/calendar/' . $prevMonth->format(
                    'Y/m',
                ),
                'nextMonthLink' => '/calendar/' . $nextMonth->format(
                    'Y/m',
                ),
            ],
        ));

        return $response;
    }
}

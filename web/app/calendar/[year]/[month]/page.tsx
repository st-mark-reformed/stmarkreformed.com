// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { notFound } from 'next/navigation';
import { ClockIcon } from '@heroicons/react/16/solid';
import Layout from '../../../layout/Layout';
import CalendarPageHeader from './CalendarPageHeader';
import GetPageData from './GetPageData';
import { CalendarPageParams } from './CalendarPageParams';
import CalendarDayHeading from './CalendarDayHeading';
import typography from '../../../typography/typography';

function calcPrevParams (currentParams: CalendarPageParams): CalendarPageParams {
    let yearInt = parseInt(currentParams.year, 10);

    let monthInt = (parseInt(currentParams.month, 10) - 1);

    if (monthInt < 1) {
        yearInt -= 1;
        monthInt = 12;
    }

    return {
        year: yearInt.toString(),
        month: monthInt.toString().padStart(2, '0'),
    };
}

function calcNextParams (currentParams: CalendarPageParams): CalendarPageParams {
    let yearInt = parseInt(currentParams.year, 10);

    let monthInt = (parseInt(currentParams.month, 10) + 1);

    if (monthInt > 12) {
        yearInt += 1;
        monthInt = 1;
    }

    return {
        year: yearInt.toString(),
        month: monthInt.toString().padStart(2, '0'),
    };
}

export default async function CalendarYearMonthPage (
    {
        params,
    }: {
        params: Promise<CalendarPageParams>;
    },
) {
    const paramsResolved = await params;

    const pageData = await GetPageData(paramsResolved);

    if (pageData === null) {
        notFound();
    }

    const {
        pagePath,
        monthDays,
        dateHeading,
        monthString,
        monthRows,
        monthEventsList,
    } = pageData;

    const prevPageParams = calcPrevParams(paramsResolved);

    const prevPageData = await GetPageData(prevPageParams);

    const nextPageParams = calcNextParams(paramsResolved);

    const nextPageData = await GetPageData(nextPageParams);

    const currentMonth = new Date();

    const currentMonthPath = [
        'calendar',
        currentMonth.getFullYear(),
        (currentMonth.getMonth() + 1).toString().padStart(2, '0'),
    ].join('/');

    let currentMonthLink = '';

    if (currentMonthPath !== pagePath) {
        currentMonthLink = [
            '',
            currentMonthPath,
        ].join('/');
    }

    let prevMonthLink = '';

    if (prevPageData) {
        prevMonthLink = `/calendar/${prevPageParams.year}/${prevPageParams.month}`;
    }

    let nextMonthLink = '';

    if (nextPageData) {
        nextMonthLink = `/calendar/${nextPageParams.year}/${nextPageParams.month}`;
    }

    let gridRowsClass = '';

    if (monthRows === 1) {
        gridRowsClass = 'grid-rows-1';
    } else if (monthRows === 2) {
        gridRowsClass = 'grid-rows-2';
    } else if (monthRows === 3) {
        gridRowsClass = 'grid-rows-3';
    } else if (monthRows === 4) {
        gridRowsClass = 'grid-rows-4';
    } else if (monthRows === 5) {
        gridRowsClass = 'grid-rows-5';
    } else if (monthRows === 6) {
        gridRowsClass = 'grid-rows-6';
    } else if (monthRows === 7) {
        gridRowsClass = 'grid-rows-7';
    }

    return (
        <Layout hero={{ heroHeading: 'Events at St. Mark' }}>
            <div className="p-4 pb-10 max-w-1800px mx-auto">
                <div className="lg:flex lg:h-full lg:flex-col">
                    <CalendarPageHeader
                        monthString={monthString}
                        dateHeading={dateHeading}
                        icsUrl="/calendar/ics"
                        currentMonthLink={currentMonthLink}
                        prevMonthLink={prevMonthLink}
                        nextMonthLink={nextMonthLink}
                    />
                    <div className="hidden shadow-md lg:flex lg:flex-auto lg:flex-col">
                        <CalendarDayHeading />
                        <div className="flex bg-gray-200 text-xs leading-6 text-gray-700 lg:flex-auto">
                            <div className={`hidden w-full lg:grid grid-cols-7 gap-px ${gridRowsClass}`}>
                                {monthDays.map((day) => (
                                    <div
                                        key={day.ymd}
                                        className={(() => {
                                            if (day.isActiveMonth) {
                                                if (day.isInPast) {
                                                    return 'relative bg-gray-100 py-2 px-3';
                                                }

                                                return 'relative bg-gray-50 py-2 px-3';
                                            }

                                            return 'relative bg-gray-200 text-gray-400 py-2 px-3';
                                        })()}
                                    >
                                        <time
                                            className={(() => {
                                                const classes = [
                                                    'inline-block',
                                                    'relative',
                                                ];

                                                if (day.isCurrentDay) {
                                                    classes.push('ml-2');
                                                }

                                                return classes.join(' ');
                                            })()}
                                            dateTime={day.ymd}
                                        >
                                            <span className="relative z-20">
                                                {day.day}
                                                {(() => {
                                                    if (day.isInPast) {
                                                        return <span className="text-gray-400 text-xxs italic"> (past)</span>;
                                                    }

                                                    return null;
                                                })()}
                                            </span>
                                            {(() => {
                                                if (!day.isCurrentDay) {
                                                    return null;
                                                }

                                                return (
                                                    <span className="block absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-lightest-red h-7 w-7 z-10 rounded-full" />
                                                );
                                            })()}
                                        </time>
                                        {(() => {
                                            if (day.events.length < 1) {
                                                return null;
                                            }

                                            return (
                                                <ol className="mt-2">
                                                    {day.events.map((event) => {
                                                        const startDate = new Date(event.startDate);

                                                        const timeString = startDate.toLocaleTimeString(
                                                            'en-US',
                                                            {
                                                                hour: 'numeric',
                                                                minute: 'numeric',
                                                                hour12: true,
                                                            },
                                                        );

                                                        return (
                                                            <li
                                                                key={event.uid + event.startDate}
                                                                className={(() => {
                                                                    const classes = [
                                                                        'mb-4',
                                                                        'py-0.5',
                                                                        'px-1.5',
                                                                    ];

                                                                    if (event.isAllDay) {
                                                                        if (day.isActiveMonth) {
                                                                            classes.push('bg-crimson');
                                                                        } else {
                                                                            classes.push('bg-lighter-red');
                                                                        }
                                                                    }

                                                                    return classes.join(' ');
                                                                }
                                                                )()}
                                                            >
                                                                <span className="group leading-tight">
                                                                    <p
                                                                        className={(() => {
                                                                            if (day.isActiveMonth) {
                                                                                if (event.isAllDay) {
                                                                                    return 'font-medium text-white leading-tight';
                                                                                }

                                                                                return 'font-medium text-gray-900 leading-tight';
                                                                            }

                                                                            if (event.isAllDay) {
                                                                                return 'font-medium text-gray-300 leading-tight';
                                                                            }

                                                                            return 'font-medium text-gray-400 leading-tight';
                                                                        })()}
                                                                    >
                                                                        {(() => {
                                                                            if (event.isAllDay) {
                                                                                return null;
                                                                            }

                                                                            return (
                                                                                <time
                                                                                    className={(() => {
                                                                                        const classes = [
                                                                                            'inline-block',
                                                                                            'font-bold',
                                                                                            'mr-0.5',
                                                                                        ];

                                                                                        if (day.isActiveMonth) {
                                                                                            classes.push('text-teal-800');
                                                                                        } else {
                                                                                            classes.push('text-gray-400');
                                                                                        }

                                                                                        return classes.join(' ');
                                                                                    })()}
                                                                                >
                                                                                    {timeString}
                                                                                    :
                                                                                </time>
                                                                            );
                                                                        })()}
                                                                        {event.summary}
                                                                    </p>
                                                                    {(() => {
                                                                        if (!event.location) {
                                                                            return null;
                                                                        }

                                                                        return (
                                                                            <p
                                                                                className="font-light text-xs"
                                                                                dangerouslySetInnerHTML={{
                                                                                    __html: typography(event.location),
                                                                                }}
                                                                            />
                                                                        );
                                                                    })()}
                                                                </span>
                                                            </li>
                                                        );
                                                    })}
                                                </ol>
                                            );
                                        })()}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                    {(() => {
                        if (monthEventsList.length < 1) {
                            return null;
                        }

                        return (
                            <div className="py-10 sm:px-6 lg:hidden">
                                <ol className="divide-y divide-gray-100 overflow-hidden rounded-lg bg-white text-sm shadow ring-1 ring-black ring-opacity-5">
                                    {monthEventsList.map((event) => {
                                        const startDate = new Date(event.startDate);

                                        const year = startDate.getFullYear();

                                        const month = startDate.getMonth() + 1;

                                        const monthName = startDate.toLocaleString(
                                            'en-US',
                                            { month: 'long' },
                                        );

                                        const day = startDate.getDate();

                                        const dayName = startDate.toLocaleDateString(
                                            'en-US',
                                            { weekday: 'long' },
                                        );

                                        const timeString = startDate.toLocaleTimeString(
                                            'en-US',
                                            {
                                                hour: 'numeric',
                                                minute: 'numeric',
                                                hour12: true,
                                            },
                                        );

                                        const timeString24hour = startDate.toLocaleTimeString(
                                            'en-US',
                                            {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                hour12: false,
                                            },
                                        );

                                        return (
                                            <li
                                                key={event.uid + event.startDate}
                                                className={(() => {
                                                    const classes = [
                                                        'group',
                                                        'flex',
                                                        'p-4',
                                                        'pr-6',
                                                    ];

                                                    if (event.isInPast) {
                                                        classes.push('bg-gray-100');
                                                    }

                                                    return classes.join(' ');
                                                })()}
                                            >
                                                <div className="flex-auto">
                                                    <p
                                                        className={(() => {
                                                            const classes = ['font-semibold'];

                                                            if (event.isInPast) {
                                                                classes.push('text-gray-400');
                                                            } else {
                                                                classes.push('text-teal-600');
                                                            }

                                                            return classes.join(' ');
                                                        })()}
                                                    >
                                                        <span dangerouslySetInnerHTML={{
                                                            __html: typography(event.summary),
                                                        }}
                                                        />
                                                        {(() => {
                                                            if (!event.isInPast) {
                                                                return null;
                                                            }

                                                            return (
                                                                <span className="text-gray-400 text-xxs italic font-normal">
                                                                    {' (past)'}
                                                                </span>
                                                            );
                                                        })()}
                                                    </p>
                                                    <p
                                                        className={(() => {
                                                            const classes = [];

                                                            if (event.isInPast) {
                                                                classes.push('text-gray-400');
                                                            } else {
                                                                classes.push('text-gray-900');
                                                            }

                                                            return classes.join(' ');
                                                        })()}
                                                        dangerouslySetInnerHTML={{
                                                            __html: typography(event.location),
                                                        }}
                                                    />
                                                    <time
                                                        dateTime={(() => {
                                                            if (event.isAllDay) {
                                                                return `${year}-${month}-${day}`;
                                                            }

                                                            return `${year}-${month}-${day}T${timeString24hour}`;
                                                        })()}
                                                        className={(() => {
                                                            const classes = [
                                                                'mt-2',
                                                                'flex',
                                                                'items-center',
                                                            ];

                                                            if (event.isInPast) {
                                                                classes.push('text-gray-400');
                                                            } else {
                                                                classes.push('text-gray-700');
                                                            }

                                                            return classes.join(' ');
                                                        })()}
                                                    >
                                                        <ClockIcon className="mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                                        {dayName}
                                                        ,
                                                        {' '}
                                                        {monthName}
                                                        ,
                                                        {' '}
                                                        {day}
                                                        {(() => {
                                                            if (event.isAllDay) {
                                                                return null;
                                                            }

                                                            return (
                                                                <>
                                                                    ,
                                                                    {' '}
                                                                    {timeString}
                                                                </>
                                                            );
                                                        })()}
                                                    </time>
                                                </div>
                                            </li>
                                        );
                                    })}
                                </ol>
                            </div>
                        );
                    })()}
                </div>
            </div>
        </Layout>
    );
}

// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import Link from 'next/link';
import typography from '../../typography/typography';
import GetUpcomingEvents from './GetUpcomingEvents';

export interface UpcomingEventsConfig {
    heading?: string;
    subHeading?: string;
}

export default async function UpcomingEvents (
    {
        heading = 'Upcoming Events',
        subHeading,
    }: UpcomingEventsConfig,
) {
    const events = await GetUpcomingEvents();

    if (events.length < 1) {
        return null;
    }

    return (
        <div className="relative bg-gray-50 pt-16 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8">
            <div className="absolute inset-0">
                <div className="bg-white h-1/3 sm:h-1/2" />
            </div>
            <div className="relative max-w-7xl mx-auto">
                {(() => {
                    if (!heading && !subHeading) {
                        return null;
                    }

                    return (
                        <div className="text-center">
                            {(() => {
                                if (!heading) {
                                    return null;
                                }

                                return (
                                    <h2
                                        className="text-3xl tracking-tight font-extrabold sm:text-4xl block bg-clip-text text-transparent bg-gradient-to-r from-teal-700 to-teal-400 pb-1"
                                        dangerouslySetInnerHTML={{
                                            __html: typography(heading),
                                        }}
                                    />
                                );
                            })()}
                            {(() => {
                                if (!subHeading) {
                                    return null;
                                }

                                return (
                                    <p
                                        className="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-2"
                                        dangerouslySetInnerHTML={{
                                            __html: typography(subHeading),
                                        }}
                                    />
                                );
                            })()}
                        </div>
                    );
                })()}
                <div className="pt-12">
                    <ul className="mt-3 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {events.map((event) => {
                            const startDate = new Date(event.startDate);

                            const threeLetterDay = startDate.toLocaleDateString(
                                'en-US',
                                { weekday: 'short' },
                            );

                            const threeLetterMonth = startDate.toLocaleDateString(
                                'en-US',
                                { month: 'short' },
                            );

                            const dayOfMonth = startDate.getDate();

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
                                    className="col-span-1 flex shadow rounded-md"
                                >
                                    <div
                                        className="flex-shrink-0 flex items-center justify-center w-16 bg-crimson-dark text-white text-sm font-medium rounded-l-md text-center"
                                    >
                                        <div>
                                            {(() => {
                                                if (event.isMultiDay) {
                                                    return null;
                                                }

                                                return (
                                                    <>
                                                        {threeLetterDay}
                                                        <br />
                                                    </>
                                                );
                                            })()}
                                            {threeLetterMonth}
                                            {' '}
                                            {dayOfMonth}
                                            {(() => {
                                                if (!event.isMultiDay) {
                                                    return null;
                                                }

                                                const endDate = new Date(event.endDate);

                                                const endThreeLetterMonth = endDate.toLocaleDateString(
                                                    'en-US',
                                                    { month: 'short' },
                                                );

                                                const endDayOfMonth = endDate.getDate();

                                                return (
                                                    <>
                                                        <div className="text-sm italic">
                                                            through
                                                        </div>
                                                        {endThreeLetterMonth}
                                                        {' '}
                                                        {endDayOfMonth}
                                                    </>
                                                );
                                            })()}
                                        </div>
                                    </div>
                                    <div className="flex-1 flex items-center justify-between border-t border-r border-b border-gray-200 bg-white rounded-r-md truncate">
                                        <div className="flex-1 px-4 py-2 text-sm truncate min-h-14 flex flex-col justify-center">
                                            {(() => {
                                                if (event.isAllDay) {
                                                    return null;
                                                }

                                                return (
                                                    <p className="text-teal-600 font-bold truncate">
                                                        {timeString}
                                                    </p>
                                                );
                                            })()}
                                            <p
                                                className="text-gray-900 font-medium truncate"
                                                dangerouslySetInnerHTML={{
                                                    __html: typography(event.summary),
                                                }}
                                            />
                                            {(() => {
                                                if (!event.location) {
                                                    return null;
                                                }

                                                return (
                                                    <p
                                                        className="text-gray-500 truncate"
                                                        dangerouslySetInnerHTML={{
                                                            __html: typography(event.location),
                                                        }}
                                                    />
                                                );
                                            })()}
                                        </div>
                                    </div>
                                </li>
                            );
                        })}
                    </ul>
                </div>
                <div className="text-center mt-16">
                    <Link
                        href="/calendar"
                        className="shadow-lg inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-md text-white bg-goldenrod hover:bg-saddle-brown-lightened-2"
                    >
                        View all events
                    </Link>
                </div>
            </div>
        </div>
    );
}

// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import Link from 'next/link';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/16/solid';
import React from 'react';
import typography from '../../../typography/typography';

export default function CalendarPageHeader (
    {
        monthString,
        dateHeading,
        icsUrl,
        currentMonthLink,
        prevMonthLink,
        nextMonthLink,
    }: {
        monthString: string;
        dateHeading: string;
        icsUrl: string;
        currentMonthLink: string;
        prevMonthLink: string;
        nextMonthLink: string;
    },
) {
    return (
        <header
            className="relative z-20 sm:flex items-center justify-between border-b border-gray-200 py-4 lg:flex-none"
        >
            <h1 className="mb-4 sm:mb-0 text-lg font-semibold text-gray-900">
                <time
                    dateTime={monthString}
                    dangerouslySetInnerHTML={{
                        __html: typography(dateHeading),
                    }}
                />
            </h1>
            <div className="flex items-center">
                <div className="mr-4 items-center flex">
                    <div className="relative">
                        <Link
                            href={icsUrl}
                            type="button"
                            className="flex items-center rounded-md border border-gray-300 bg-white py-2 px-3 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 select-none"
                            id="menu-button"
                            title="Copy and paste link into the subscribe action of your calendar"
                        >
                            Subscribe
                        </Link>
                    </div>
                </div>
                {(() => (
                    <div className="mr-4 items-center flex">
                        <div className="relative">
                            <Link
                                href={currentMonthLink}
                                type="button"
                                className={(() => {
                                    const classes = [
                                        'flex items-center',
                                        'rounded-md border border-gray-300',
                                        'bg-white',
                                        'py-2 px-3',
                                        'text-sm font-medium',
                                        'shadow-sm',
                                    ];

                                    if (!currentMonthLink) {
                                        classes.push(
                                            'text-gray-200',
                                            'cursor-default',
                                        );
                                    } else {
                                        classes.push(
                                            'text-gray-700',
                                            'hover:text-gray-900',
                                            'md:hover:bg-gray-50',
                                        );
                                    }

                                    return classes.join(' ');
                                })()}
                                id="menu-button"
                            >
                                Current Month
                            </Link>
                        </div>
                    </div>
                ))()}
                <div className="flex items-center rounded-md shadow-sm md:items-stretch">
                    <Link
                        href={prevMonthLink}
                        type="button"
                        className={(() => {
                            const classes = [
                                'flex items-center justify-center',
                                'rounded-l-md border border-r-0 border-gray-300',
                                'bg-white',
                                'py-2 pl-3 pr-4',
                                'focus:relative',
                                'md:w-9 md:px-2',
                            ];

                            if (!prevMonthLink) {
                                classes.push(
                                    'text-gray-200',
                                    'cursor-default',
                                );
                            } else {
                                classes.push(
                                    'text-gray-700',
                                    'hover:text-gray-900',
                                    'md:hover:bg-gray-50',
                                );
                            }

                            return classes.join(' ');
                        })()}
                    >
                        <span className="sr-only">Previous month</span>
                        <ChevronLeftIcon className="h-5 w-5" />
                    </Link>
                    <Link
                        href={nextMonthLink}
                        type="button"
                        className={(() => {
                            const classes = [
                                'flex items-center justify-center',
                                'rounded-r-md',
                                'border border-gray-300',
                                'bg-white',
                                'py-2 pl-4 pr-3',
                                'focus:relative',
                                'md:w-9 md:px-2',
                            ];

                            if (!nextMonthLink) {
                                classes.push(
                                    'text-gray-200',
                                    'cursor-default',
                                );
                            } else {
                                classes.push(
                                    'text-gray-700',
                                    'hover:text-gray-900',
                                    'md:hover:bg-gray-50',
                                );
                            }

                            return classes.join(' ');
                        })()}
                    >
                        <span className="sr-only">Next month</span>
                        <ChevronRightIcon className="h-5 w-5" />
                    </Link>
                </div>
            </div>
        </header>
    );
}

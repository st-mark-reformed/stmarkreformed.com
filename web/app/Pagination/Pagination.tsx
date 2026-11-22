import React from 'react';
import Link from 'next/link';
import CreatePages from './CreatePages';
import { PaginationParams } from './PaginationParams';

export default function Pagination (
    {
        baseUrl = '',
        currentPage = 1,
        totalPages = 1,
        pad = 2,
        className = '',
        queryString = '',
        darkModeAware = false,
    }: PaginationParams,
) {
    if (totalPages < 2) {
        return null;
    }

    const darkClasses = darkModeAware
        ? {
            pageCount: 'dark:text-gray-400',
            link: 'dark:border-white/10 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/20 dark:hover:text-white',
            current: 'dark:border-white/10 dark:bg-transparent dark:text-gray-500',
        }
        : {
            pageCount: '',
            link: '',
            current: '',
        };

    if (queryString && !queryString.startsWith('?')) {
        queryString = `?${queryString}`;
    }

    const prevPage = currentPage <= 1 ? null : currentPage - 1;

    const prevPageLink = (() => {
        if (!prevPage) {
            return null;
        }

        if (prevPage < 2) {
            return `${baseUrl}${queryString}`;
        }

        return `${baseUrl}/page/${prevPage}${queryString}`;
    })();

    const nextPage = currentPage >= totalPages ? null : currentPage + 1;

    const nextPageLink = !nextPage ? null : `${baseUrl}/page/${nextPage}${queryString}`;

    const firstPageLink = currentPage <= (pad + 1) ? null : `${baseUrl}${queryString}`;

    const lastPageLink = (() => {
        if (currentPage + pad >= totalPages) {
            return null;
        }

        return `${baseUrl}/page/${totalPages}${queryString}`;
    })();

    const pages = CreatePages({
        baseUrl,
        currentPage,
        totalPages,
        pad,
        queryString,
    });

    return (
        <div className={`${className} not-prose`}>
            <div>
                <p className={`text-sm text-gray-700 ${darkClasses.pageCount}`}>
                    Page
                    {' '}
                    {currentPage}
                    {' '}
                    of
                    {' '}
                    {totalPages}
                </p>
            </div>
            <div className="py-3 flex items-center justify-between">
                <div className="flex-1 flex justify-between sm:hidden">
                    {(() => {
                        if (!prevPageLink) {
                            return <span className="relative inline-flex items-center px-4 py-2" />;
                        }

                        return (
                            <Link
                                href={prevPageLink}
                                className={`relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 not-prose ${darkClasses.link}`}
                            >
                                Previous
                            </Link>
                        );
                    })()}
                    {(() => {
                        if (!nextPageLink) {
                            return <span className="ml-3 relative inline-flex items-center px-4 py-2" />;
                        }

                        return (
                            <Link
                                href={nextPageLink}
                                className={`ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 not-prose ${darkClasses.link}`}
                            >
                                Next
                            </Link>
                        );
                    })()}
                </div>
                <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            {(() => {
                                if (!firstPageLink) {
                                    return null;
                                }

                                return (
                                    <Link
                                        href={firstPageLink}
                                        className={`relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 not-prose ${darkClasses.link}`}
                                    >
                                        <span className="sr-only">First</span>
                                        <svg
                                            className="h-5 w-5"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                        <svg
                                            className="h-5 w-5 -ml-3.5"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </Link>
                                );
                            })()}
                            {(() => {
                                if (!prevPageLink) {
                                    return null;
                                }

                                return (
                                    <Link
                                        href={prevPageLink}
                                        className={(() => {
                                            const classes = [
                                                'relative',
                                                'inline-flex',
                                                'items-center',
                                                'px-2',
                                                'py-2',
                                                'border',
                                                'border-gray-300',
                                                'bg-white',
                                                'text-sm',
                                                'font-medium',
                                                'text-gray-500',
                                                'hover:bg-gray-50',
                                                'not-prose',
                                                darkClasses.link,
                                            ];

                                            if (!firstPageLink) {
                                                classes.push('rounded-l-md');
                                            }

                                            return classes.join(' ');
                                        })()}
                                    >
                                        <span className="sr-only">Previous</span>
                                        <svg
                                            className="h-5 w-5"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </Link>
                                );
                            })()}
                            {pages.map((page, index) => {
                                const key = `${page.href}-${page.label}`;

                                const loopCount = index + 1;

                                const isFirst = index === 0;

                                const isLast = loopCount === pages.length;

                                if (page.isActive) {
                                    return (
                                        <span
                                            key={key}
                                            className={(() => {
                                                const classes = [
                                                    'relative',
                                                    'inline-flex',
                                                    'items-center',
                                                    'px-4',
                                                    'py-2',
                                                    'border',
                                                    'border-gray-300',
                                                    'bg-white',
                                                    'text-sm',
                                                    'font-medium',
                                                    'text-gray-400',
                                                    darkClasses.current,
                                                ];

                                                if (isFirst && !firstPageLink && !prevPageLink) {
                                                    classes.push('rounded-l-md');
                                                }

                                                if (isLast && !lastPageLink && !nextPageLink) {
                                                    classes.push('rounded-r-md');
                                                }

                                                return classes.join(' ');
                                            })()}
                                        >
                                            {page.label}
                                        </span>
                                    );
                                }

                                return (
                                    <Link
                                        key={key}
                                        href={page.href}
                                        className={(() => {
                                            const classes = [
                                                'relative',
                                                'inline-flex',
                                                'items-center',
                                                'px-4',
                                                'py-2',
                                                'border',
                                                'border-gray-300',
                                                'bg-white',
                                                'text-sm',
                                                'font-medium',
                                                'text-gray-700',
                                                'hover:bg-gray-50',
                                                'not-prose',
                                                darkClasses.link,
                                            ];

                                            if (isFirst && !firstPageLink && !prevPageLink) {
                                                classes.push('rounded-l-md');
                                            }

                                            if (isLast && !lastPageLink && !nextPageLink) {
                                                classes.push('rounded-r-md');
                                            }

                                            return classes.join(' ');
                                        })()}
                                    >
                                        {page.label}
                                    </Link>
                                );
                            })}
                            {(() => {
                                if (!nextPageLink) {
                                    return null;
                                }

                                return (
                                    <Link
                                        href={nextPageLink}
                                        className={(() => {
                                            const classes = [
                                                'relative',
                                                'inline-flex',
                                                'items-center',
                                                'px-2',
                                                'py-2 border',
                                                'border-gray-300',
                                                'bg-white',
                                                'text-sm',
                                                'font-medium',
                                                'text-gray-500',
                                                'hover:bg-gray-50',
                                                'not-prose',
                                                darkClasses.link,
                                            ];

                                            if (!lastPageLink) {
                                                classes.push('rounded-r-md');
                                            }

                                            return classes.join(' ');
                                        })()}
                                    >
                                        <span className="sr-only">Next</span>
                                        <svg
                                            className="h-5 w-5"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </Link>
                                );
                            })()}
                            {(() => {
                                if (!lastPageLink) {
                                    return null;
                                }

                                return (
                                    <Link
                                        href={lastPageLink}
                                        className={`relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-r-md not-prose ${darkClasses.link}`}
                                    >
                                        <span className="sr-only">Next</span>
                                        <svg
                                            className="h-5 w-5"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                        <svg
                                            className="h-5 w-5 -ml-3.5"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </Link>
                                );
                            })()}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    );
}

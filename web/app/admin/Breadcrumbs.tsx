import React from 'react';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/20/solid';
import Link from 'next/link';

interface Breadcrumb {
    content: string;
    href: string;
}

export default function Breadcrumbs (
    {
        crumbs = [],
    }: {
        crumbs?: Breadcrumb[];
    },
) {
    crumbs = [
        {
            content: 'Home',
            href: '/',
        },
        {
            content: 'Admin',
            href: '/admin',
        },
        ...crumbs,
    ];

    const lastBreadcrumb = crumbs[crumbs.length - 1];

    return (
        <div>
            <nav aria-label="Back" className="sm:hidden">
                <Link
                    href={lastBreadcrumb.href}
                    className="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                >
                    <ChevronLeftIcon
                        aria-hidden="true"
                        className="mr-1 -ml-1 size-5 shrink-0 text-gray-400 dark:text-gray-500"
                    />
                    {lastBreadcrumb.content}
                </Link>
            </nav>
            <nav aria-label="Breadcrumb" className="hidden sm:flex">
                <ol className="flex items-center space-x-1">
                    {crumbs.map((crumb, index) => (
                        <li key={crumb.href}>
                            <div className="flex items-center">
                                {(() => {
                                    if (index === 0) {
                                        return null;
                                    }

                                    return (
                                        <ChevronRightIcon aria-hidden="true" className="size-5 shrink-0 text-gray-400 dark:text-gray-500" />
                                    );
                                })()}
                                <Link
                                    href={crumb.href}
                                    className="mx-0.5 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                >
                                    {crumb.content}
                                </Link>
                            </div>
                        </li>
                    ))}
                </ol>
            </nav>
        </div>
    );
}

// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import Link from 'next/link';
import { HomeIcon } from '@heroicons/react/20/solid';
import smartypants from 'smartypants';

export type CurrentBreadcrumbItem = {
    value: string;
};

export type BreadcrumbItem = CurrentBreadcrumbItem & {
    href: string;
};

export type BreadcrumbItems = Array<BreadcrumbItem>;

export default function Breadcrumbs (
    {
        breadcrumbs = [],
        currentBreadcrumb,
    }: {
        breadcrumbs?: BreadcrumbItems;
        currentBreadcrumb: CurrentBreadcrumbItem;
    },
) {
    return (
        <nav className="flex mb-0.5" aria-label="Breadcrumb">
            <ol className="bg-white shadow px-6 flex space-x-4 w-full dark:bg-stone-800 dark:border-b dark:border-stone-700">
                <li className="flex">
                    <div className="flex items-center">
                        <Link href="/" className="text-gray-400 hover:text-gray-500 dark:text-stone-500 dark:hover:text-stone-300">
                            <HomeIcon className="flex-shrink-0 h-5 w-5" />
                            <span className="sr-only">Home</span>
                        </Link>
                    </div>
                </li>
                {breadcrumbs.map((breadcrumb) => (
                    <li key={breadcrumb.href} className="flex">
                        <div className="flex items-center">
                            <svg
                                className="flex-shrink-0 w-6 h-full text-gray-200 dark:text-stone-700"
                                viewBox="0 0 24 44"
                                preserveAspectRatio="none"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
                            </svg>
                            <Link
                                href={breadcrumb.href}
                                className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-stone-400 dark:hover:text-stone-200"
                                dangerouslySetInnerHTML={{
                                    __html: smartypants(breadcrumb.value),
                                }}
                            />
                        </div>
                    </li>
                ))}
                <li className="flex">
                    <div className="flex items-center">
                        <svg
                            className="flex-shrink-0 w-6 h-full text-gray-200 dark:text-stone-700"
                            viewBox="0 0 24 44"
                            preserveAspectRatio="none"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
                        </svg>
                        <span
                            className="ml-4 text-sm font-medium text-gray-500 dark:text-stone-400"
                            dangerouslySetInnerHTML={{
                                __html: smartypants(currentBreadcrumb.value),
                            }}
                        />
                    </div>
                </li>
            </ol>
        </nav>
    );
}

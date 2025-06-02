// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import Link from 'next/link';
import { ArrowDownTrayIcon } from '@heroicons/react/24/outline';
import { ResourceItem } from './repository/ResourceItem';
import typography from '../typography/typography';

export default function ResourceListing (
    {
        entry,
    }: {
        entry: ResourceItem;
    },
) {
    const href = `/resources-test/${entry.slug}`;

    return (
        <article className="flex flex-col items-start justify-between border border-gray-200 p-3 sm:p-6 shadow-md rounded-lg">
            <div className="group relative mb-3">
                <h3 className="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
                    <Link
                        href={href}
                        dangerouslySetInnerHTML={{
                            __html: typography(entry.title),
                        }}
                    />
                </h3>
            </div>
            {(() => {
                if (entry.resourceDownloads.length !== 1 || entry.body) {
                    return null;
                }

                return (
                    <Link
                        href={`/uploads/general/resources/${entry.slug}/${entry.resourceDownloads[0].filename}`}
                        className="mt-auto w-full rounded-md bg-crimson px-3.5 py-2.5 text-sm text-white shadow-sm hover:bg-crimson-dark text-center mb-2"
                        download
                    >
                        Download &ldquo;<span className="font-semibold">{entry.resourceDownloads[0].filename}</span>&rdquo; <span className="-mt-1 align-middle inline-block w-4 h-4"><ArrowDownTrayIcon /></span>
                    </Link>
                );
            })()}
            <Link
                href={href}
                className="mt-auto w-full rounded-md bg-crimson px-3.5 py-2.5 text-sm text-white shadow-sm hover:bg-crimson-dark text-center"
            >
                Go to resource page &raquo;
            </Link>
        </article>
    );
}

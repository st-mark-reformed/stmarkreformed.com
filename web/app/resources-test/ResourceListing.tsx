import React from 'react';
import Link from 'next/link';
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
            <Link
                href={href}
                className="mt-auto w-full rounded-md bg-crimson px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-crimson-dark text-center"
            >
                Go to resource &raquo;
            </Link>
        </article>
    );
}

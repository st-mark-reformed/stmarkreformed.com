// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { NewsItem } from './repository/NewsItem';
import typography from '../typography/typography';

export default function NewsListing (
    {
        entry,
    }: {
        entry: NewsItem;
    },
) {
    const href = `/news-test/${entry.slug}`;

    return (
        <article className="flex flex-col items-start justify-between border border-gray-200 p-3 sm:p-6 shadow-md rounded-lg">
            <div className="flex items-center gap-x-4 text-xs">
                <div className="text-gray-500">
                    {entry.readableDate}
                </div>
            </div>
            <div className="group relative mb-3">
                <h3 className="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
                    <a
                        href={href}
                        dangerouslySetInnerHTML={{
                            __html: typography(entry.title),
                        }}
                    />
                </h3>
                <div
                    className="mt-5 line-clamp-6 text-sm/6 text-gray-600 listing-hide-elements"
                    dangerouslySetInnerHTML={{
                        __html: typography(entry.bodyOnlyContent),
                    }}
                />
            </div>
            <a
                href={href}
                className="mt-auto w-full rounded-md bg-crimson px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-crimson-dark text-center"
            >
                Read Entry
            </a>
        </article>
    );
}

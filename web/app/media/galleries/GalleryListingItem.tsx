// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { GalleryEntryFull } from './GalleryEntry';
import typography from '../../typography/typography';

export default function GalleryListingItem (
    {
        entry,
    }: {
        entry: GalleryEntryFull;
    },
) {
    return (
        <div className="flex flex-col rounded-lg shadow-lg overflow-hidden">
            <div className="flex-shrink-0">
                <Link href={entry.href}>
                    <div className="h-64 relative">
                        <Image
                            className="object-cover"
                            src={entry.posterUrl}
                            alt=""
                            fill
                        />
                    </div>
                </Link>
            </div>
            <div className="flex-1 bg-white p-6 flex flex-col justify-between">
                <div className="flex-1">
                    <Link
                        href={entry.href}
                        className="block mt-2"
                    >
                        <p
                            className="text-xl font-semibold text-gray-900"
                            dangerouslySetInnerHTML={{
                                __html: typography(entry.title),
                            }}
                        />
                    </Link>
                </div>
                <a
                    href={entry.href}
                    className="mt-3 inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-crimson hover:bg-crimson-dark"
                >
                    View Gallery
                </a>
            </div>
        </div>
    );
}

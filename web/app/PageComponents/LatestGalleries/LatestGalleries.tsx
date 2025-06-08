// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import FindAllGalleryEntries from '../../media/galleries/repository/FindAllGalleryEntries';
import typography from '../../typography/typography';
import GalleryListingItem from '../../media/galleries/GalleryListingItem';

export interface LatestGalleriesConfig {
    heading?: string;
    subHeading?: string;
}

export default function LatestGalleries (
    {
        heading = 'Latest Galleries',
        subHeading,
    }: LatestGalleriesConfig,
) {
    const entries = FindAllGalleryEntries().slice(0, 3);

    if (entries.length < 1) {
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
                <div className="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
                    {entries.map((entry) => <GalleryListingItem key={entry.slug} entry={entry} />)}
                </div>
                <div className="text-center mt-16">
                    <Link
                        href="/media/galleries"
                        className="shadow-lg inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-md text-white bg-goldenrod hover:bg-saddle-brown-lightened-2"
                    >
                        View all galleries
                    </Link>
                </div>
            </div>
        </div>
    );
}

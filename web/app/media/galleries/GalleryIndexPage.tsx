// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import Link from 'next/link';
import Image from 'next/image';
import React from 'react';
import FindAllGalleryEntriesByPage from './repository/FindAllGalleryEntriesByPage';
import Pagination from '../../Pagination/Pagination';
import Layout from '../../layout/Layout';
import typography from '../../typography/typography';

export default function GalleryIndexPage (
    {
        pageNum,
    }: {
        pageNum: number;
    },
) {
    const { totalPages, entries } = FindAllGalleryEntriesByPage(pageNum);

    const pagination = (
        <Pagination
            baseUrl="/media/galleries"
            currentPage={pageNum}
            totalPages={totalPages}
            className="pb-2"
        />
    );

    return (
        <Layout hero={{ heroHeading: 'Photo Galleries' }}>
            <div className="relative py-16 px-4 sm:px-6 lg:px-8">
                <div className="relative max-w-7xl mx-auto">
                    {pagination}
                    <div className="max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
                        {entries.map((entry) => (
                            <div
                                key={entry.slug}
                                className="flex flex-col rounded-lg shadow-lg overflow-hidden"
                            >
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
                        ))}
                    </div>
                    {(() => {
                        if (totalPages < 2) {
                            return null;
                        }

                        return (
                            <div className="pt-6">
                                {pagination}
                            </div>
                        );
                    })()}
                </div>
            </div>
        </Layout>
    );
}

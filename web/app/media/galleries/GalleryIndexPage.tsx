// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import Link from 'next/link';
import Image from 'next/image';
import React from 'react';
import FindAllGalleryEntriesByPage from './repository/FindAllGalleryEntriesByPage';
import Pagination from '../../Pagination/Pagination';
import Layout from '../../layout/Layout';
import typography from '../../typography/typography';
import GalleryListingItem from './GalleryListingItem';

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
                        {entries.map((entry) => <GalleryListingItem key={entry.slug} entry={entry} />)}
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

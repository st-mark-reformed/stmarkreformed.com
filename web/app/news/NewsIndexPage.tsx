import React from 'react';
import { notFound } from 'next/navigation';
import FindNewsItemsByPage from './repository/FindNewsItemsByPage';
import Pagination from '../Pagination/Pagination';
import Layout from '../layout/Layout';
import NewsListing from './NewsListing';

export default async function NewsIndexPage (
    {
        pageNum,
    }: {
        pageNum: number;
    },
) {
    const newsItemResults = await FindNewsItemsByPage(pageNum);

    if (newsItemResults === null) {
        notFound();
    }

    const pagination = (
        <Pagination
            baseUrl="/news"
            currentPage={newsItemResults.currentPage}
            totalPages={newsItemResults.totalPages}
        />
    );

    return (
        <Layout hero={{ heroHeading: 'News' }}>
            <div className="bg-white py-12">
                <div className="mx-auto max-w-7xl px-6 lg:px-8">
                    {pagination}
                    <div className="mx-auto grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-3 py-12">
                        {newsItemResults.entries.map((entry) => (
                            <NewsListing
                                key={`${entry.slug}-${entry.readableDate}`}
                                entry={entry}
                            />
                        ))}
                    </div>
                    {pagination}
                </div>
            </div>
        </Layout>
    );
}

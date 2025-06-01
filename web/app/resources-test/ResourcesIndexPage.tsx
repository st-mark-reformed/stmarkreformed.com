import React from 'react';
import { notFound } from 'next/navigation';
import FindResourceItemsByPage from './repository/FindResourceItemsByPage';
import Pagination from '../Pagination/Pagination';
import Layout from '../layout/Layout';
import ResourceListing from './ResourceListing';

export default async function ResourcesIndexPage (
    {
        pageNum,
    }: {
        pageNum: number;
    },
) {
    const resourceItemResults = await FindResourceItemsByPage(pageNum);

    if (resourceItemResults === null) {
        notFound();
    }

    const pagination = (
        <Pagination
            baseUrl="/resources-test"
            currentPage={resourceItemResults.currentPage}
            totalPages={resourceItemResults.totalPages}
        />
    );

    return (
        <Layout hero={{ heroHeading: 'Resources' }}>
            <div className="bg-white py-12">
                <div className="mx-auto max-w-7xl px-6 lg:px-8">
                    {pagination}
                    <div className="mx-auto grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-3 py-12">
                        {resourceItemResults.entries.map((entry) => (
                            <ResourceListing
                                key={entry.slug}
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

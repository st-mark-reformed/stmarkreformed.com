import React from 'react';
import { notFound } from 'next/navigation';
import { MagnifyingGlassIcon } from '@heroicons/react/24/solid';
import FindAllMessagesByPage from './repository/FindAllMessagesByPage';
import Pagination from '../../Pagination/Pagination';
import MessagesLayout from './MessagesLayout';
import EntryDisplay from './EntryDisplay';
import SearchForm from './search/SearchForm';

export default async function MessagesListingPage (
    {
        pageNum,
    }: {
        pageNum: number;
    },
) {
    const pageData = await FindAllMessagesByPage(pageNum);

    if (pageData === null) {
        notFound();
    }

    const totalEntries = pageData.entries.length;

    const pagination = (
        <Pagination
            baseUrl="/media/messages-test"
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
        />
    );

    const topOfBodyContent = (
        <div className="px-8 pt-4 relative">
            <SearchForm />
            {pagination}
        </div>
    );

    return (
        <MessagesLayout
            heroHeading="Messages"
            topOfBodyContent={topOfBodyContent}
            bottomOfBodyContent={<div className="px-8 pb-4">{pagination}</div>}
        >
            {pageData.entries.map((entry, i) => (
                <EntryDisplay
                    key={`${entry.slug}-${entry.postDate}`}
                    baseUri="/media/messages-test"
                    entry={entry}
                    showBorder={(i + 1) < totalEntries}
                    showPermalink
                />
            ))}
        </MessagesLayout>
    );
}

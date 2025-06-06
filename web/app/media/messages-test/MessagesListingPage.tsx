import React from 'react';
import { notFound } from 'next/navigation';
import { headers } from 'next/headers';
import FindAllMessagesByPage from './repository/FindAllMessagesByPage';
import Pagination from '../../Pagination/Pagination';
import MessagesLayout from './MessagesLayout';
import EntryDisplay from './EntryDisplay';
import SearchForm from './search/SearchForm';
import { MessagesSearchParamsParent } from './search/MessagesSearchParams';
import Breadcrumbs from '../../Breadcrumbs';
import FindAllByOptions from './repository/FindAllByOptions';
import FindAllSeriesOptions from './repository/FindAllSeriesOptions';
import SearchMessagesByPage from './repository/SearchMessagesByPage';

export default async function MessagesListingPage (
    {
        pageNum,
        messagesSearchParams,
    }: {
        pageNum: number;
        messagesSearchParams: MessagesSearchParamsParent;
    },
) {
    const headersList = await headers();
    const queryString = headersList.get('middleware-search-params');

    const { hasAnyParams } = messagesSearchParams;

    const pageData = messagesSearchParams.hasAnyParams
        ? await SearchMessagesByPage(pageNum, messagesSearchParams)
        : await FindAllMessagesByPage(pageNum);

    if (hasAnyParams && !pageData) {
        return <>TODOD</>;
    }

    if (pageData === null) {
        notFound();
    }

    const byOptions = await FindAllByOptions();

    const seriesOptions = await FindAllSeriesOptions();

    const totalEntries = pageData.entries.length;

    const pagination = (
        <Pagination
            baseUrl="/media/messages-test"
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
            queryString={queryString || ''}
        />
    );

    const topOfBodyContent = (
        <>
            {(() => {
                if (!hasAnyParams) {
                    return null;
                }

                return (
                    <Breadcrumbs
                        breadcrumbs={[{
                            value: 'Messages',
                            href: '/media/messages-test',
                        }]}
                        currentBreadcrumb={{ value: 'Search Results' }}
                    />
                );
            })()}
            <div className="px-8 pt-4 relative">
                <SearchForm byOptions={byOptions} seriesOptions={seriesOptions} />
                {pagination}
            </div>
        </>
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

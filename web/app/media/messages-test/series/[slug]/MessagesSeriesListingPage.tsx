import React from 'react';
import { notFound } from 'next/navigation';
import Pagination from '../../../../Pagination/Pagination';
import Breadcrumbs from '../../../../Breadcrumbs';
import FindAllMessagesBySeriesByPage from '../../repository/FindAllMessagesBySeriesByPage';
import MessagesLayout from '../../MessagesLayout';
import EntryDisplay from '../../EntryDisplay';

export default async function MessagesSeriesListingPage (
    {
        slug,
        pageNum,
    }: {
        slug: string;
        pageNum: number;
    },
) {
    const pageData = await FindAllMessagesBySeriesByPage(slug, pageNum);

    if (pageData === null) {
        notFound();
    }

    const totalEntries = pageData.entries.length;

    const pagination = (
        <Pagination
            baseUrl={`/media/messages-test/series/${slug}`}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
        />
    );

    const topOfBodyContent = (
        <>
            <Breadcrumbs
                breadcrumbs={[{
                    value: 'Messages',
                    href: '/media/messages-test',
                }]}
                currentBreadcrumb={{ value: `series: ${pageData.seriesName}` }}
            />
            <div className="px-8 pt-4">{pagination}</div>
        </>
    );

    return (
        <MessagesLayout
            heroHeading={`Messages Series: ${pageData.seriesName}`}
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

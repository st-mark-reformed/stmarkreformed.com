import React from 'react';
import { notFound } from 'next/navigation';
import FindAllMessagesBySpeakerByPage from '../../repository/FindAllMessagesBySpeakerByPage';
import Pagination from '../../../../Pagination/Pagination';
import Breadcrumbs from '../../../../Breadcrumbs';
import MessagesLayout from '../../MessagesLayout';
import EntryDisplay from '../../EntryDisplay';

export default async function MessagesByListingPage (
    {
        slug,
        pageNum,
    }: {
        slug: string;
        pageNum: number;
    },
) {
    const pageData = await FindAllMessagesBySpeakerByPage(slug, pageNum);

    if (pageData === null) {
        notFound();
    }

    const totalEntries = pageData.entries.length;

    const pagination = (
        <Pagination
            baseUrl={`/media/messages/by/${slug}`}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
        />
    );

    const topOfBodyContent = (
        <>
            <Breadcrumbs
                breadcrumbs={[{
                    value: 'Messages',
                    href: '/media/messages',
                }]}
                currentBreadcrumb={{ value: `by ${pageData.byName}` }}
            />
            <div className="px-8 pt-4">{pagination}</div>
        </>
    );

    return (
        <MessagesLayout
            heroHeading={`Messages by ${pageData.byName}`}
            topOfBodyContent={topOfBodyContent}
            bottomOfBodyContent={<div className="px-8 pb-4">{pagination}</div>}
        >
            {pageData.entries.map((entry, i) => (
                <EntryDisplay
                    key={`${entry.slug}-${entry.postDate}`}
                    baseUri="/media/messages"
                    entry={entry}
                    showBorder={(i + 1) < totalEntries}
                    showPermalink
                />
            ))}
        </MessagesLayout>
    );
}

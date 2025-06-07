import React from 'react';
import { notFound } from 'next/navigation';
import GetPageData from './GetPageData';
import MemberLayout from '../MemberLayout';
import EntryDisplay from '../../media/messages/EntryDisplay';
import Pagination from '../../Pagination/Pagination';

export default async function MembersInternalMediaPage (
    {
        pageNum,
    }: {
        pageNum: number;
    },
) {
    const pageData = await GetPageData(pageNum);

    if (pageData === null) {
        notFound();
    }

    const totalEntries = pageData.entries.length;

    const pagination = (
        <Pagination
            baseUrl="/members/internal-media"
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
        />
    );

    return (
        <MemberLayout
            heroHeading="Internal Media"
            activeNavHref="/members/internal-media"
            topOfBodyContent={<div className="px-8 pt-4">{pagination}</div>}
            bottomOfBodyContent={<div className="px-8 pb-4">{pagination}</div>}
        >
            {pageData.entries.map((entry, i) => (
                <EntryDisplay
                    key={`${entry.slug}-${entry.postDate}`}
                    baseUri="/members/internal-media"
                    entry={entry}
                    showBorder={(i + 1) < totalEntries}
                    showPermalink
                    useInternalAudioUrlScheme
                />
            ))}
        </MemberLayout>
    );
}

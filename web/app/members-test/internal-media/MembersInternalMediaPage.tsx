import React from 'react';
import { notFound } from 'next/navigation';
import GetPageData from './GetPageData';
import MemberLayout from '../MemberLayout';
import AudioListing from '../../audio/AudioListing';
import EntryDisplay from './EntryDisplay';
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
            baseUrl="/members-test/internal-media"
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
        />
    );

    return (
        <MemberLayout
            heroHeading="Internal Media"
            activeNavHref="/members-test/internal-media"
            topOfBodyContent={<div className="px-8 pt-4">{pagination}</div>}
            bottomOfBodyContent={<div className="px-8 pb-4">{pagination}</div>}
        >
            {pageData.entries.map((entry, i) => (
                <EntryDisplay
                    key={`${entry.slug}-${entry.postDate}`}
                    entry={entry}
                    showBorder={(i + 1) < totalEntries}
                    showPermalink
                />
            ))}
        </MemberLayout>
    );
}

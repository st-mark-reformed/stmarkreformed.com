import React from 'react';
import { notFound } from 'next/navigation';
import GetPageData from './GetPageData';
import Pagination from '../../../../Pagination/Pagination';
import Breadcrumbs from '../../../../Breadcrumbs';
import MemberLayout from '../../../MemberLayout';
import EntryDisplay from '../../EntryDisplay';

export default async function MembersInternalMediaSeriesPage (
    {
        slug,
        pageNum,
    }: {
        slug: string;
        pageNum: number;
    },
) {
    const pageData = await GetPageData(slug, pageNum);

    if (pageData === null) {
        notFound();
    }

    const totalEntries = pageData.entries.length;

    const pagination = (
        <Pagination
            baseUrl={`/members-test/internal-media/series/${slug}`}
            currentPage={pageData.currentPage}
            totalPages={pageData.totalPages}
        />
    );

    const topOfBodyContent = (
        <>
            <Breadcrumbs
                breadcrumbs={[{
                    value: 'Internal Media',
                    href: '/members-test/internal-media',
                }]}
                currentBreadcrumb={{ value: `series: ${pageData.seriesName}` }}
            />
            <div className="px-8 pt-4">{pagination}</div>
        </>
    );

    return (
        <MemberLayout
            heroHeading={`Internal Media Series: ${pageData.seriesName}`}
            activeNavHref="/members-test/internal-media"
            topOfBodyContent={topOfBodyContent}
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

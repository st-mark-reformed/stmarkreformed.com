import React from 'react';
import { notFound } from 'next/navigation';
import GetPageData from './GetPageData';
import MemberLayout from '../../MemberLayout';
import EntryDisplay from '../EntryDisplay';
import Breadcrumbs from '../../../Breadcrumbs';

export default async function Page (
    {
        params,
    }: {
        params: Promise<{
            slug: string;
        }>;
    },
) {
    const paramsResolved = await params;

    const pageData = await GetPageData(paramsResolved.slug);

    if (pageData === null) {
        notFound();
    }

    return (
        <MemberLayout
            heroHeading="Internal Media"
            activeNavHref="/members-test/internal-media"
            topOfBodyContent={(
                <Breadcrumbs
                    breadcrumbs={[{
                        value: 'Internal Media',
                        href: '/members-test/internal-media',
                    }]}
                    currentBreadcrumb={{ value: pageData.entry.title }}
                />
            )}
        >
            <EntryDisplay
                entry={pageData.entry}
            />
        </MemberLayout>
    );
}

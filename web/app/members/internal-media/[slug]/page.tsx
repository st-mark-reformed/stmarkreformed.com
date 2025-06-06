import React from 'react';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import GetPageData from './GetPageData';
import MemberLayout from '../../MemberLayout';
import EntryDisplay from '../../../media/messages/EntryDisplay';
import Breadcrumbs from '../../../Breadcrumbs';
import { createPageTitle } from '../../../createPageTitle';

export const dynamic = 'force-dynamic';

export async function generateMetadata (
    {
        params,
    }: {
        params: Promise<{
            slug: string;
        }>;
    },
): Promise<Metadata> {
    const paramsResolved = await params;

    const pageData = await GetPageData(paramsResolved.slug);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            pageData.entry.title,
            'Internal Media',
            'Members Area',
        ]),
    };
}

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
            activeNavHref="/members/internal-media"
            topOfBodyContent={(
                <Breadcrumbs
                    breadcrumbs={[{
                        value: 'Internal Media',
                        href: '/members/internal-media',
                    }]}
                    currentBreadcrumb={{ value: pageData.entry.title }}
                />
            )}
        >
            <EntryDisplay
                baseUri="/members/internal-media"
                entry={pageData.entry}
                useInternalAudioUrlScheme
            />
        </MemberLayout>
    );
}

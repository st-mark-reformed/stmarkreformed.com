import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import MembersInternalMediaSeriesPage from './MembersInternalMediaSeriesPage';
import GetPageData from './GetPageData';
import { createPageTitle } from '../../../../createPageTitle';

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

    const pageData = await GetPageData(paramsResolved.slug, 1);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Series: ${pageData.seriesName}`,
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

    return (
        <MembersInternalMediaSeriesPage
            slug={paramsResolved.slug}
            pageNum={1}
        />
    );
}

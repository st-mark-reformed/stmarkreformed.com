import { notFound } from 'next/navigation';
import React from 'react';
import { Metadata } from 'next';
import MembersInternalMediaSeriesPage from '../../MembersInternalMediaSeriesPage';
import { createPageTitle } from '../../../../../../createPageTitle';
import GetPageData from '../../GetPageData';

export const dynamic = 'force-dynamic';

export async function generateMetadata (
    {
        params,
    }: {
        params: Promise<{
            slug: string;
            pageNum: string;
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
            `Page ${paramsResolved.pageNum}`,
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
            pageNum: string;
        }>;
    },
) {
    const paramsResolved = await params;

    const isNumeric = /^\d+$/.test(paramsResolved.pageNum);

    if (!isNumeric) {
        notFound();
    }

    const pageNumInt = parseInt(paramsResolved.pageNum, 10);

    if (pageNumInt < 2) {
        notFound();
    }

    return (
        <MembersInternalMediaSeriesPage
            slug={paramsResolved.slug}
            pageNum={pageNumInt}
        />
    );
}

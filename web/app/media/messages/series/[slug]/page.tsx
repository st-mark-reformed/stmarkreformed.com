import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { createPageTitle } from '../../../../createPageTitle';
import FindAllMessagesBySeriesByPage from '../../repository/FindAllMessagesBySeriesByPage';
import MessagesSeriesListingPage from './MessagesSeriesListingPage';

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
    const { slug } = await params;

    const pageData = await FindAllMessagesBySeriesByPage(slug, 1);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Series: ${pageData.seriesName}`,
            'Messages',
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
    const { slug } = await params;

    return (
        <MessagesSeriesListingPage
            slug={slug}
            pageNum={1}
        />
    );
}

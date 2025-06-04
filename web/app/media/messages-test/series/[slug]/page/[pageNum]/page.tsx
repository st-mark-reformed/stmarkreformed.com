import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { createPageTitle } from '../../../../../../createPageTitle';
import FindAllMessagesBySeriesByPage from '../../../../repository/FindAllMessagesBySeriesByPage';
import MessagesSeriesListingPage from '../../MessagesSeriesListingPage';

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
    const { slug, pageNum } = await params;

    const pageData = await FindAllMessagesBySeriesByPage(slug, 1);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Page ${pageNum}`,
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
            pageNum: string;
        }>;
    },
) {
    const { slug, pageNum } = await params;

    const isNumeric = /^\d+$/.test(pageNum);

    if (!isNumeric) {
        notFound();
    }

    const pageNumInt = parseInt(pageNum, 10);

    if (pageNumInt < 2) {
        notFound();
    }

    return (
        <MessagesSeriesListingPage
            slug={slug}
            pageNum={pageNumInt}
        />
    );
}

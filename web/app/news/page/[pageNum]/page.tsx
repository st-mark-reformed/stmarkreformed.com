import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { createPageTitle } from '../../../createPageTitle';
import NewsIndexPage from '../../NewsIndexPage';

export const dynamic = 'force-dynamic';

export async function generateMetadata (
    {
        params,
    }: {
        params: Promise<{
            pageNum: string;
        }>;
    },
): Promise<Metadata> {
    const { pageNum } = await params;

    return {
        title: createPageTitle([
            `Page ${pageNum}`,
            'News',
        ]),
        alternates: {
            types: {
                'application/rss+xml': '/news/rss',
            },
        },
    };
}

export default async function Page (
    {
        params,
    }: {
        params: Promise<{
            pageNum: string;
        }>;
    },
) {
    const { pageNum } = await params;

    const isNumeric = /^\d+$/.test(pageNum);

    if (!isNumeric) {
        notFound();
    }

    const pageNumInt = parseInt(pageNum, 10);

    if (pageNumInt < 2) {
        notFound();
    }

    return (
        <NewsIndexPage
            sectionHandle="news"
            baseUri="/news"
            heading="News"
            pageNum={pageNumInt}
        />
    );
}

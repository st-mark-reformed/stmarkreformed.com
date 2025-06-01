import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { createPageTitle } from '../../../createPageTitle';
import ResourcesIndexPage from '../../ResourcesIndexPage';

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
            'Resources',
        ]),
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

    return <ResourcesIndexPage pageNum={pageNumInt} />;
}

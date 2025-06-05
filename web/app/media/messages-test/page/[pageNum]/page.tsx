import React from 'react';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import { createPageTitle } from '../../../../createPageTitle';
import MessagesListingPage from '../../MessagesListingPage';
import { createMessagesSearchParamsFromRaw, RawSearchParams } from '../../search/MessagesSearchParams';

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
            'Messages',
        ]),
    };
}

export default async function Page (
    {
        params,
        searchParams,
    }: {
        params: Promise<{
            pageNum: string;
        }>;
        searchParams: Promise<RawSearchParams>;
    },
) {
    const rawParams = await searchParams;

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
        <MessagesListingPage
            pageNum={pageNumInt}
            messagesSearchParams={createMessagesSearchParamsFromRaw(rawParams)}
        />
    );
}

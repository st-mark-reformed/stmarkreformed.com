import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../createPageTitle';
import MessagesListingPage from './MessagesListingPage';
import { createMessagesSearchParamsFromRaw, RawSearchParams } from './search/MessagesSearchParams';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle('Messages'),
};

export default async function Page (
    {
        searchParams,
    }: {
        searchParams: Promise<RawSearchParams>;
    },
) {
    const rawParams = await searchParams;

    return (
        <MessagesListingPage
            pageNum={1}
            messagesSearchParams={createMessagesSearchParamsFromRaw(rawParams)}
        />
    );
}

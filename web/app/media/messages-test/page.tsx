import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../createPageTitle';
import MessagesListingPage from './MessagesListingPage';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle('Messages'),
};

export default async function Page () {
    return <MessagesListingPage pageNum={1} />;
}

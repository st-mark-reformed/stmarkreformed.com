import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../createPageTitle';
import NewsIndexPage from './NewsIndexPage';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle('News'),
};

export default async function Page () {
    return <NewsIndexPage pageNum={1} />;
}

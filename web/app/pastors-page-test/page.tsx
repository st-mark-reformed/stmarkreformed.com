import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../createPageTitle';
import NewsIndexPage from '../news/NewsIndexPage';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle("Pastor's Page"),
    alternates: {
        types: {
            'application/rss+xml': '/pastors-page-test/rss',
        },
    },
};

export default async function Page () {
    return (
        <NewsIndexPage
            sectionHandle="pastorsPage"
            baseUri="/pastors-page-test"
            heading="Pastor's Page"
            pageNum={1}
        />
    );
}

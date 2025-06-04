import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import MessagesListingByPage from './MessagesListingByPage';
import { createPageTitle } from '../../../../createPageTitle';
import FindAllMessagesBySpeakerByPage from '../../repository/FindAllMessagesBySpeakerByPage';

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

    const pageData = await FindAllMessagesBySpeakerByPage(slug, 1);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Messages by ${pageData.byName}`,
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
        <MessagesListingByPage
            slug={slug}
            pageNum={1}
        />
    );
}

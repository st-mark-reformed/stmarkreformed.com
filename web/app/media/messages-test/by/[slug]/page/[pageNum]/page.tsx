import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { createPageTitle } from '../../../../../../createPageTitle';
import MessagesListingByPage from '../../MessagesListingByPage';
import FindAllMessagesBySpeakerByPage from '../../../../repository/FindAllMessagesBySpeakerByPage';

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

    const pageData = await FindAllMessagesBySpeakerByPage(slug, 1);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Page ${pageNum}`,
            `Media by ${pageData.byName}`,
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
        <MessagesListingByPage
            slug={slug}
            pageNum={pageNumInt}
        />
    );
}

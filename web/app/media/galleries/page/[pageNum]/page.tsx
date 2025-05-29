import { notFound } from 'next/navigation';
import React from 'react';
import { Metadata } from 'next';
import GalleryIndexPage from '../../GalleryIndexPage';
import { createPageTitle } from '../../../../createPageTitle';
import FindAllGalleryEntriesByPage from '../../repository/FindAllGalleryEntriesByPage';

export const dynamic = 'force-static';

export async function generateStaticParams (): Promise<Array<{
    pageNum: string;
}>> {
    const { totalPages } = FindAllGalleryEntriesByPage(1);

    return Array.from({ length: totalPages }, (_, index) => ({
        pageNum: (index + 1).toString(),
    })).filter(({ pageNum }) => pageNum !== '1');
}

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
            'Photo Galleries',
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

    return <GalleryIndexPage pageNum={pageNumInt} />;
}

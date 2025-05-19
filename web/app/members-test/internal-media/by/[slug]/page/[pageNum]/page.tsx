import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import MembersInternalMediaByPage from '../../MembersInternalMediaByPage';
import GetPageData from '../../GetPageData';
import { createPageTitle } from '../../../../../../createPageTitle';

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
    const paramsResolved = await params;

    const pageData = await GetPageData(paramsResolved.slug, 1);

    if (pageData === null) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Page ${paramsResolved.pageNum}`,
            `Media by ${pageData.byName}`,
            'Internal Media',
            'Members Area',
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
    const paramsResolved = await params;

    const isNumeric = /^\d+$/.test(paramsResolved.pageNum);

    if (!isNumeric) {
        notFound();
    }

    const pageNumInt = parseInt(paramsResolved.pageNum, 10);

    if (pageNumInt < 2) {
        notFound();
    }

    return (
        <MembersInternalMediaByPage
            slug={paramsResolved.slug}
            pageNum={pageNumInt}
        />
    );
}

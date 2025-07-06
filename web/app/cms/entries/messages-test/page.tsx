import React, { Suspense } from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import CmsLayout from '../../layout/CmsLayout';
import { createPageTitle } from '../../../createPageTitle';
import MessageListingPage from './MessageListingPage';
import PartialPageLoading from '../../../PartialPageLoading';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Messages',
            'CMS',
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
    let { pageNum } = await params;

    pageNum = (pageNum ?? '1').toString();

    const isNumeric = /^\d+$/.test(pageNum);

    if (!isNumeric) {
        notFound();
    }

    const pageNumInt = parseInt(pageNum, 10);

    if (pageNumInt < 1) {
        notFound();
    }

    return (
        <CmsLayout
            breadcrumbs={{
                breadcrumbs: [
                    {
                        value: 'CMS',
                        href: '/cms',
                    },
                ],
                currentBreadcrumb: { value: 'Messages' },
            }}
        >
            <Suspense fallback={<PartialPageLoading />}>
                <MessageListingPage pageNum={pageNumInt} />
            </Suspense>
        </CmsLayout>
    );
}

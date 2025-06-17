import { Metadata } from 'next';
import React, { Suspense } from 'react';
import { createPageTitle } from '../../../../createPageTitle';
import CmsLayout from '../../../layout/CmsLayout';
import PartialPageLoading from '../../../../PartialPageLoading';
import PageInner from './PageInner';

export const dynamic = 'force-dynamic';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Series Manager',
            'Messages',
            'CMS',
        ]),
    };
}

export default async function Page () {
    return (
        <CmsLayout
            breadcrumbs={{
                breadcrumbs: [
                    {
                        value: 'CMS',
                        href: '/cms',
                    },
                    {
                        value: 'Messages',
                        href: '/cms/entries/messages-test',
                    },
                ],
                currentBreadcrumb: { value: 'Series Manager' },
            }}
        >
            <Suspense fallback={<PartialPageLoading />}>
                <PageInner />
            </Suspense>
        </CmsLayout>
    );
}

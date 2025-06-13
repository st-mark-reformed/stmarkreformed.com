import { Metadata } from 'next';
import React, { Suspense } from 'react';
import { createPageTitle } from '../../createPageTitle';
import CmsLayout from '../layout/CmsLayout';
import PartialPageLoading from '../../PartialPageLoading';
import PageInner from './PageInner';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Profiles',
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
                ],
                currentBreadcrumb: { value: 'Profiles' },
            }}
        >
            <Suspense fallback={<PartialPageLoading />}>
                <PageInner />
            </Suspense>
        </CmsLayout>
    );
}

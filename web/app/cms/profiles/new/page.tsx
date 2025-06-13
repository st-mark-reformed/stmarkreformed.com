import { Metadata } from 'next';
import React from 'react';
import { createPageTitle } from '../../../createPageTitle';
import { RequestFactory } from '../../../api/request/RequestFactory';
import CmsLayout from '../../layout/CmsLayout';
import ApiResponseGate from '../../ApiResponseGate';
import PageInner from './PageInner';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'New Profile',
            'Messages',
            'CMS',
        ]),
    };
}

export default async function Page () {
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/has-cms-access',
        cacheSeconds: 0,
    });

    return (
        <CmsLayout
            breadcrumbs={{
                breadcrumbs: [
                    {
                        value: 'CMS',
                        href: '/cms',
                    },
                    {
                        value: 'Profiles',
                        href: '/cms/profiles',
                    },
                ],
                currentBreadcrumb: { value: 'New' },
            }}
        >
            <ApiResponseGate apiResponse={apiResponse}>
                <PageInner />
            </ApiResponseGate>
        </CmsLayout>
    );
}

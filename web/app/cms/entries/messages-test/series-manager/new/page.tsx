import React from 'react';
import { Metadata } from 'next';
import { RequestFactory } from '../../../../../api/request/RequestFactory';
import CmsLayout from '../../../../layout/CmsLayout';
import ApiResponseGate from '../../../../ApiResponseGate';
import EditSeriesForm from '../EditSeries/EditSeriesForm';
import { createPageTitle } from '../../../../../createPageTitle';

export const dynamic = 'force-dynamic';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'New Series',
            'Series Manager',
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
                <EditSeriesForm />
            </ApiResponseGate>
        </CmsLayout>
    );
}

import { Metadata } from 'next';
import React from 'react';
import { createPageTitle } from '../../../createPageTitle';
import ApiResponseGate from '../../ApiResponseGate';
import CmsLayout from '../../layout/CmsLayout';
import { RequestFactory } from '../../../api/request/RequestFactory';

export const dynamic = 'force-dynamic';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Edit Profile',
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
            id: string;
        }>;
    },
) {
    const { id } = await params;

    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: `/cms/profiles/${id}`,
        cacheSeconds: 0,
    });

    // TODO
    console.log(apiResponse);

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
                currentBreadcrumb: { value: 'Edit' },
            }}
        >
            <ApiResponseGate apiResponse={apiResponse}>
                TODO
            </ApiResponseGate>
        </CmsLayout>
    );
}

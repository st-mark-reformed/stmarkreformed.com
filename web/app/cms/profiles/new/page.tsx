import { Metadata } from 'next';
import React from 'react';
import { createPageTitle } from '../../../createPageTitle';
import { RequestFactory } from '../../../api/request/RequestFactory';
import CmsLayout from '../../layout/CmsLayout';
import ApiResponseGate from '../../ApiResponseGate';
import EditProfileForm from '../EditProfile/EditProfileForm';

export const dynamic = 'force-dynamic';

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
                <EditProfileForm />
            </ApiResponseGate>
        </CmsLayout>
    );
}

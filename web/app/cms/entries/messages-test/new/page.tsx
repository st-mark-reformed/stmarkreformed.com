import React from 'react';
import { Metadata } from 'next';
import CmsLayout from '../../../layout/CmsLayout';
import { createPageTitle } from '../../../../createPageTitle';
import PageInner from './PageInner';
import { RequestFactory } from '../../../../api/request/RequestFactory';
import ApiResponseGate from '../../../ApiResponseGate';
import EditMessageForm from '../EditMessage/EditMessageForm';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'New Entry',
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
                        value: 'Messages',
                        href: '/cms/entries/messages-test',
                    },
                ],
                currentBreadcrumb: { value: 'Create' },
            }}
        >
            <ApiResponseGate apiResponse={apiResponse}>
                <EditMessageForm />
            </ApiResponseGate>
        </CmsLayout>
    );
}

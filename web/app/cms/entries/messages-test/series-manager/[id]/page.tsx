import { Metadata } from 'next';
import React from 'react';
import { createPageTitle } from '../../../../../createPageTitle';
import { RequestFactory } from '../../../../../api/request/RequestFactory';
import { MessageSeries } from '../MessageSeries';
import CmsLayout from '../../../../layout/CmsLayout';
import ApiResponseGate from '../../../../ApiResponseGate';
import EditSeriesForm from '../EditSeries/EditSeriesForm';

export const dynamic = 'force-dynamic';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Edit Series',
            'Series Manager',
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
        uri: `/cms/entries/messages/series/${id}`,
        cacheSeconds: 0,
    });

    const series = apiResponse.json as unknown as MessageSeries & {
        id: string;
    };

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
                    {
                        value: 'Series Manager',
                        href: '/cms/entries/messages-test/series-manager',
                    },
                ],
                currentBreadcrumb: { value: 'Edit' },
            }}
        >
            <ApiResponseGate apiResponse={apiResponse}>
                <EditSeriesForm
                    id={series.id}
                    initialFormData={series}
                />
            </ApiResponseGate>
        </CmsLayout>
    );
}

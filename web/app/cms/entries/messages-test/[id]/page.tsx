import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../../createPageTitle';
import { RequestFactory } from '../../../../api/request/RequestFactory';
import { Message } from '../Message';
import CmsLayout from '../../../layout/CmsLayout';
import ApiResponseGate from '../../../ApiResponseGate';
import EditSeriesForm from '../series-manager/EditSeries/EditSeriesForm';
import EditMessageForm from '../EditMessage/EditMessageForm';

export const dynamic = 'force-dynamic';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Edit Message',
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
        uri: `/cms/entries/messages/${id}`,
        cacheSeconds: 0,
    });

    const message = apiResponse.json as unknown as Message;

    let date = null;

    if (message.date) {
        date = new Date(`${message.date.replace(' ', 'T')}Z`);
    }

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
                currentBreadcrumb: { value: 'Edit Message' },
            }}
        >
            <ApiResponseGate apiResponse={apiResponse}>
                <EditMessageForm
                    id={message.id}
                    initialFormData={{
                        published: message.isPublished,
                        date,
                        title: message.title,
                        text: message.text,
                        speakerId: message.speaker?.id || '',
                        seriesId: message.series?.id || '',
                        audioFile: message.audioFileName,
                    }}
                />
            </ApiResponseGate>
        </CmsLayout>
    );
}

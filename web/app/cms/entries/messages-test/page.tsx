import React from 'react';
import { Metadata } from 'next';
import { PlusIcon } from '@heroicons/react/16/solid';
import { DocumentIcon } from '@heroicons/react/24/outline';
import CmsLayout from '../../layout/CmsLayout';
import { createPageTitle } from '../../../createPageTitle';
import PageHeader from '../../layout/PageHeader';
import EmptyState from '../../layout/EmptyState';
import { RequestFactory } from '../../../api/request/RequestFactory';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Messages',
            'CMS',
        ]),
    };
}

export default async function Page () {
    // For now, just make sure we're logged in
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/userinfo',
        cacheSeconds: 0,
    });

    console.log(apiResponse);

    return (
        <CmsLayout apiResponse={apiResponse}>
            <div className="mb-4 ">
                <PageHeader
                    title="Messages"
                    buttons={[
                        {
                            id: 'fileManager',
                            type: 'secondary',
                            content: (
                                <>
                                    <DocumentIcon className="h-5 w-5 mr-1" />
                                    File Manager
                                </>
                            ),
                            href: '/cms/entries/messages-test/file-manager',
                        },
                        {
                            id: 'newEntry',
                            type: 'primary',
                            content: (
                                <>
                                    <PlusIcon className="h-5 w-5 mr-1" />
                                    New Entry
                                </>
                            ),
                            href: '/cms/entries/messages-test/new-entry',
                        },
                    ]}
                />
            </div>
            <EmptyState
                itemNameSingular="Entry"
                itemNamePlural="Entries"
                buttonHref="/cms/entries/messages-test/new-entry"
            />
        </CmsLayout>
    );
}
